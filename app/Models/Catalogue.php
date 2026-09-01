<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Catalogue extends Model
{
    protected $table = 'catalogues';

    protected $fillable = [
        'title',
        'short_title',
        'short_description',
        'price_regular',
        'price_final',
        'catalogue_type',
        'discount_type',
        'discount_value',
        'is_discount_active',
        'service_type',
        'fixed_date',
        'start_time',
        'end_time',
        'details_file',
        'story_guide_file',
        'module_file',
        'is_feature',
        'is_trending',
        'is_popular',
        'healthcare_quality_improvement',
        'patient_safety_risk_management',
        'status',
        'credit_earn',
        'ce_credit_total_required',
        'validity_years',
        'certification_seal',
        'credential_statement',
        'overview_video',
    ];

    protected $casts = [
        'is_discount_active' => 'boolean',
        'credit_earn' => 'float',
        'ce_credit_total_required' => 'float',
        'validity_years' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($catalogue) {
            if (in_array($catalogue->catalogue_type, ['free', 'members only'])) {
                $catalogue->price_regular = 0.00;
                $catalogue->price_final = 0.00;
            } else {
                if ($catalogue->is_discount_active) {
                    if ($catalogue->discount_type === 'percentage') {
                        $catalogue->price_final = max(0.00, $catalogue->price_regular - ($catalogue->price_regular * ($catalogue->discount_value / 100)));
                    } else {
                        $catalogue->price_final = max(0.00, $catalogue->price_regular - $catalogue->discount_value);
                    }
                } else {
                    $catalogue->price_final = $catalogue->price_regular ?? 0.00;
                }
            }
        });

        static::updating(function ($catalogue) {
            if (in_array($catalogue->catalogue_type, ['free', 'members only'])) {
                $catalogue->price_regular = 0.00;
                $catalogue->price_final = 0.00;
            } else {
                if ($catalogue->is_discount_active) {
                    if ($catalogue->discount_type === 'percentage') {
                        $catalogue->price_final = max(0.00, $catalogue->price_regular - ($catalogue->price_regular * ($catalogue->discount_value / 100)));
                    } else {
                        $catalogue->price_final = max(0.00, $catalogue->price_regular - $catalogue->discount_value);
                    }
                } else {
                    $catalogue->price_final = $catalogue->price_regular ?? 0.00;
                }
            }
        });
    }

    /**
     * Get the features for the catalog item.
     */
    public function features()
    {
        return $this->hasMany(CatalogueFeature::class, 'catalogue_id');
    }

    /**
     * Get the resources for the catalog item.
     */
    public function resources()
    {
        return $this->hasMany(CatalogueResource::class, 'catalogue_id');
    }

    /**
     * Get the exams for the catalog item.
     */
    public function exams()
    {
        return $this->hasMany(CatalogueExam::class, 'catalogue_id');
    }

    /**
     * Get the purchases for the catalog item.
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'catalogue_id');
    }

    /**
     * Get the live links for the catalog item.
     */
    public function liveLinks()
    {
        return $this->hasMany(CatalogueLiveLink::class, 'catalogue_id');
    }

    /**
     * Get the videos for the catalog item.
     */
    public function videos()
    {
        return $this->hasMany(CatalogueVideo::class, 'catalogue_id');
    }

    /**
     * Get the video links for the catalog item.
     */
    public function videoLinks()
    {
        return $this->hasMany(CatalogueVideoLink::class, 'catalogue_id');
    }

    /**
     * Standalone HTML documents attached to this catalogue - modules, story
     * guides, toolkits and worksheets.
     */
    public function htmlResources()
    {
        return $this->hasMany(CatalogueHtmlResource::class, 'catalogue_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /**
     * Get the discount percentage dynamically based on user status (Non-Stacking Policy)
     */
    public function getDiscountPercentageForUser(?User $user): float
    {
        $priceRegular = (float) $this->price_regular;
        if ($priceRegular <= 0) {
            return 0.00;
        }
        
        $priceFinal = $this->calculateFinalPriceForUser($user);
        $discountAmount = max(0.00, $priceRegular - $priceFinal);
        
        return (float) (($discountAmount / $priceRegular) * 100);
    }

    /**
     * Calculate the final price dynamically based on user membership (Non-Stacking Policy)
     */
    public function calculateFinalPriceForUser(?User $user): float
    {
        if (in_array($this->catalogue_type, ['free', 'members only'])) {
            return 0.00;
        }

        $priceRegular = (float) $this->price_regular;
        $priceFinal = (float) $this->price_final;

        if ($user) {
            $activeMembership = $user->active_membership;
            if ($activeMembership) {
                $membershipDiscount = (float) $activeMembership->discount_percentage;
                if ($membershipDiscount > 0) {
                    $membershipPrice = max(0.00, $priceRegular - ($priceRegular * ($membershipDiscount / 100)));
                    // Return the lower of the membership price and standard catalogue final price
                    return (float) min($priceFinal, $membershipPrice);
                }
            }
        }

        return $priceFinal;
    }
}
