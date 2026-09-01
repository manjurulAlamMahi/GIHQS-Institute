<?php

namespace App\Services;

use App\Models\CatalogueHtmlResource;
use App\Models\HtmlResourceLicense;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Decides who may open an uploaded HTML document, and issues the one-time
 * tickets that let an iframe load one.
 *
 * Tickets exist because an <iframe src="..."> cannot send an Authorization
 * header. Rather than weakening authentication to accommodate that - accepting a
 * token from the query string or a cookie, both of which leak - the frontend
 * exchanges its bearer token for a short-lived, single-use URL. That URL carries
 * its own authority for one fetch and is worthless afterwards, which also makes
 * a copied link useless.
 */
class HtmlResourceAccessService
{
    public const REASON_NO_COURSE_ACCESS = 'no_course_access';
    public const REASON_LICENSE_REQUIRED = 'license_required';
    public const REASON_LICENSE_EXPIRED  = 'license_expired';
    public const REASON_LICENSE_REVOKED  = 'license_revoked';

    /** How long an unused ticket survives. */
    public const TICKET_TTL_SECONDS = 60;

    private const CACHE_PREFIX = 'html-ticket:';

    public function __construct(private ExamEligibilityService $eligibility)
    {
    }

    /**
     * May this user open this document?
     *
     * @return array{0:bool, 1:?string} [allowed, reason]
     */
    public function check(CatalogueHtmlResource $resource, ?User $user): array
    {
        if ($resource->is_public) {
            return [true, null];
        }

        if (!$user || !$resource->catalogue) {
            return [false, self::REASON_NO_COURSE_ACCESS];
        }

        if (!$this->eligibility->hasCatalogueAccess($user, $resource->catalogue)) {
            return [false, self::REASON_NO_COURSE_ACCESS];
        }

        // No key configured means no licence is required.
        if (!$resource->requiresLicense()) {
            return [true, null];
        }

        $license = $this->licenseFor($resource, $user);

        if (!$license) {
            return [false, self::REASON_LICENSE_REQUIRED];
        }

        if ($license->isRevoked()) {
            return [false, self::REASON_LICENSE_REVOKED];
        }

        if ($license->isExpired()) {
            return [false, self::REASON_LICENSE_EXPIRED];
        }

        return [true, null];
    }

    public function licenseFor(CatalogueHtmlResource $resource, User $user): ?HtmlResourceLicense
    {
        return HtmlResourceLicense::where('user_id', $user->id)
            ->where('catalogue_html_resource_id', $resource->id)
            ->first();
    }

    /**
     * Redeem an access key. Returns the licence, or null when the key is wrong.
     *
     * Comparison is constant time so the key cannot be recovered by timing.
     */
    public function redeem(CatalogueHtmlResource $resource, User $user, string $key): ?HtmlResourceLicense
    {
        if (!$resource->requiresLicense() || !hash_equals((string) $resource->access_key, $key)) {
            return null;
        }

        $expiresAt = $resource->license_validity_days
            ? now()->addDays($resource->license_validity_days)
            : null;

        $license = $this->licenseFor($resource, $user);

        if ($license) {
            // Re-redeeming refreshes an expired or revoked licence rather than
            // creating a duplicate.
            $license->update([
                'granted_at' => now(),
                'expires_at' => $expiresAt,
                'revoked_at' => null,
            ]);

            return $license;
        }

        return HtmlResourceLicense::create([
            'user_id'                    => $user->id,
            'catalogue_html_resource_id' => $resource->id,
            'granted_at'                 => now(),
            'expires_at'                 => $expiresAt,
        ]);
    }

    /**
     * Mint a single-use ticket for a viewer that has already been authorised.
     */
    public function issueTicket(CatalogueHtmlResource $resource, ?User $user): string
    {
        $ticket = Str::random(43);

        Cache::put(
            self::CACHE_PREFIX . $ticket,
            ['resource_id' => $resource->id, 'user_id' => $user?->id],
            now()->addSeconds(self::TICKET_TTL_SECONDS)
        );

        return $ticket;
    }

    /**
     * Consume a ticket, returning the resource id it was issued for.
     *
     * The entry is deleted before the caller does anything with it, so a ticket
     * cannot be replayed even if two requests race.
     */
    public function consumeTicket(string $ticket): ?int
    {
        $payload = Cache::pull(self::CACHE_PREFIX . $ticket);

        return $payload['resource_id'] ?? null;
    }
}
