<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Purchase extends Model
{
    use HasFactory;

    protected $table = 'purchases';

    protected $fillable = [
        'user_id',
        'purchase_type',
        'catalogue_id',
        'membership_package_id',
        'advisory_request_id',
        'accreditation_application_id',
        'order_id',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'amount',
        'refunded_amount',
        'price_regular',
        'price_purchased',
        'discount_amount',
        'discount_percentage',
        'price_type',
        'payment_status',
        'order_status',
        'payment_method',
        'expires_at',
        'refund_request_status',
        'refund_request_reason',
        'refund_requested_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'refunded_amount' => 'decimal:2',
        'refund_requested_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($purchase) {
            $purchase->order_id = self::generateUniqueOrderId($purchase->purchase_type);
        });
    }

    public static function generateUniqueOrderId($type)
    {
        $prefix = match ($type) {
            'membership' => 'ORD-MEM-',
            'advisory'   => 'ORD-ADV-',
            'accreditation' => 'ORD-ACC-',
            default      => 'ORD-CAT-',
        };
        do {
            $orderId = $prefix . strtoupper(Str::random(8));
        } while (self::where('order_id', $orderId)->exists());

        return $orderId;
    }

    /**
     * Get the user that made the purchase.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the purchased catalogue item.
     */
    public function catalogue()
    {
        return $this->belongsTo(Catalogue::class, 'catalogue_id');
    }

    /**
     * Get the purchased membership package.
     */
    public function membershipPackage()
    {
        return $this->belongsTo(MembershipPackage::class, 'membership_package_id')->withTrashed();
    }

    /**
     * Get the advisory request associated with the purchase.
     */
    public function advisoryRequest()
    {
        return $this->belongsTo(AdvisoryRequest::class, 'advisory_request_id');
    }

    /**
     * Get the accreditation application associated with the purchase.
     */
    public function accreditationApplication()
    {
        return $this->belongsTo(AccreditationApplication::class, 'accreditation_application_id');
    }

    /**
     * Get the refunds issued for the purchase.
     */
    public function refunds()
    {
        return $this->hasMany(PurchaseRefund::class, 'purchase_id');
    }
}
