<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        // Basic info
        'first_name',
        'last_name',
        'full_name',
        'country',
        'username',
        'email',
        'phone',
        'avatar',

        // Auth & role
        'password',
        'role',
        'language',
        'status',

        // Location
        'user_latitude',
        'user_longitude',

        // Address
        'address',
        'city',
        'zip',
        'bio',

        // OTP
        'otp',
        'otp_verified',
        'otp_attempts',
        'otp_expired_at',
        'otp_verified_at',

        // Social login
        'google_id',
        'facebook_id',
        'provider',
        'provider_token',

        // Device & notification
        'device_id',
        'fcm_token',

        // Activity
        'last_login_at',

        // Stripe Subscriptions
        'stripe_customer_id',
        'stripe_subscription_id',
        'stripe_subscription_status',
        'stripe_subscription_period_start',
        'stripe_subscription_period_end',
        'stripe_next_renewal_at',
        'stripe_subscription_cancel_at_period_end',
        'membership_package_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'otp_verified' => 'boolean',
            'stripe_subscription_period_start' => 'datetime',
            'stripe_subscription_period_end' => 'datetime',
            'stripe_next_renewal_at' => 'datetime',
            'stripe_subscription_cancel_at_period_end' => 'boolean',
        ];
    }

    // JWT Methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Get the user's active membership purchase.
     */
    public function activeMembershipPurchase()
    {
        return $this->hasOne(Purchase::class, 'user_id')
            ->where('purchase_type', 'membership')
            ->where('payment_status', 'paid')
            ->latest();
    }

    /**
     * Get the membership package associated with the user's Stripe subscription.
     */
    public function membershipPackage()
    {
        return $this->belongsTo(MembershipPackage::class, 'membership_package_id')->withTrashed();
    }

    /**
     * Get the user's active membership package.
     */
    public function getActiveMembershipAttribute()
    {
        if ($this->stripe_subscription_id && in_array($this->stripe_subscription_status, ['active', 'trialing'])) {
            return $this->membershipPackage;
        }

        $purchase = $this->activeMembershipPurchase;
        if (!$purchase) {
            return null;
        }

        if ($purchase->expires_at && now()->greaterThan($purchase->expires_at)) {
            return null;
        }

        return $purchase->membershipPackage;
    }

    /**
     * Get the user's active paid membership package (e.g., Premium).
     * Standard is a free/default membership package, which does not count as a paid membership.
     */
    public function getActivePaidMembershipAttribute()
    {
        $membership = $this->active_membership;
        if (!$membership) {
            return null;
        }

        if (strcasecmp($membership->name, 'Standard') === 0 || (float)$membership->price <= 0) {
            return null;
        }

        return $membership;
    }

    /**
     * Get the discount percentage for the active membership.
     */
    public function getDiscountPercentage(): float
    {
        $membership = $this->active_membership;
        return $membership ? (float) $membership->discount_percentage : 0.00;
    }

    /**
     * Get the maximum allowed exam attempts for the active membership.
     */
    public function getMaxExamAttempts(): int
    {
        $membership = $this->active_membership;
        return $membership ? (int) $membership->exam_attempt_limit : 1;
    }

    /**
     * Assign default Standard (or first active) Membership package to the user.
     */
    public function assignDefaultMembership()
    {
        $userRole = $this->role ?: 'user';
        if ($userRole === 'user' && !$this->activeMembershipPurchase) {
            $defaultPackage = MembershipPackage::where('name', 'Standard')->where('status', 1)->first()
                            ?? MembershipPackage::where('status', 1)->orderBy('price', 'asc')->first();

            if ($defaultPackage) {
                Purchase::create([
                    'user_id'               => $this->id,
                    'purchase_type'         => 'membership',
                    'membership_package_id' => $defaultPackage->id,
                    'order_id'              => Purchase::generateUniqueOrderId('membership'),
                    'payment_status'        => 'paid',
                    'amount'                => (float) $defaultPackage->price,
                    'expires_at'            => null,
                ]);
            }
        }
    }

    /**
     * Check if the user has a valid active (non-expired) certification.
     */
    public function hasValidCertification($catalogueId): bool
    {
        $catalogue = \App\Models\Catalogue::with('exams')->find($catalogueId);
        if (!$catalogue || $catalogue->service_type !== 'Certification') {
            return false;
        }

        $examIds = $catalogue->exams->pluck('id');
        if ($examIds->isEmpty()) {
            return false;
        }

        $latestPassedResult = \App\Models\UserExamResult::where('user_id', $this->id)
            ->whereIn('catalogue_exam_id', $examIds)
            ->where('status', 'passed')
            ->latest('id')
            ->first();

        if (!$latestPassedResult) {
            return false;
        }

        $validityYears = (int) ($catalogue->validity_years ?? 1);
        $expiryDate = $latestPassedResult->created_at->addYears($validityYears);

        return now()->lt($expiryDate);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($user) {
            if ($user->isDirty(['first_name', 'last_name']) || empty($user->full_name)) {
                $user->full_name = trim($user->first_name . ' ' . $user->last_name);
            }
        });
    }
}
