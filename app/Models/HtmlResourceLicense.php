<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One user's redeemed licence for one HTML resource.
 *
 * A licence is valid when it has not been revoked and has not expired. Expiry is
 * fixed at redemption from the resource's license_validity_days, so changing that
 * setting later does not retroactively move existing licences.
 */
class HtmlResourceLicense extends Model
{
    protected $table = 'html_resource_licenses';

    protected $fillable = [
        'user_id',
        'catalogue_html_resource_id',
        'granted_at',
        'expires_at',
        'revoked_at',
    ];

    protected $casts = [
        'granted_at' => 'datetime',
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resource(): BelongsTo
    {
        return $this->belongsTo(CatalogueHtmlResource::class, 'catalogue_html_resource_id');
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return !$this->isRevoked() && !$this->isExpired();
    }
}
