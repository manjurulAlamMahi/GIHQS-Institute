<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseRefund extends Model
{
    use HasFactory;

    protected $table = 'purchase_refunds';

    protected $fillable = [
        'purchase_id',
        'stripe_refund_id',
        'amount',
        'reason',
        'admin_id',
        'status',
    ];

    /**
     * Get the purchase associated with the refund.
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    /**
     * Get the administrator who issued the refund.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
