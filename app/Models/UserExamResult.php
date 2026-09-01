<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserExamResult extends Model
{
    use HasFactory;

    protected $table = 'user_exam_results';

    protected $fillable = [
        'user_id',
        'catalogue_exam_id',
        'score',
        'points_available',
        'percentage',
        'percentage_passmark',
        'status',
        'duration',
        'ip_address',
        'start_time',
        'end_time',
        'classmarker_result_id',
        'certificate_serial_number',
        'certificate_url',
        'download_certificate',
        'view_results_url',
        'raw_payload',
        'category_results',
    ];

    protected $casts = [
        'raw_payload' => 'array',
        'score' => 'float',
        'points_available' => 'float',
        'percentage' => 'float',
        'percentage_passmark' => 'float',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'category_results' => 'array',
    ];

    /**
     * Has this attempt actually been passed?
     *
     * Certificate data is only ever exposed for a passed attempt. ClassMarker
     * issues its own certificate against its own pass mark, which can be lower
     * than the pass mark configured here; that certificate stays in raw_payload
     * for auditing but must never surface as a downloadable credential.
     */
    public function isPassed(): bool
    {
        return strtolower((string) ($this->attributes['status'] ?? '')) === 'passed';
    }

    /**
     * Accessor for certificate_serial_number with fallback to raw_payload.
     */
    protected function certificateSerialNumber(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$this->isPassed()) {
                    return null;
                }
                if (!empty($value)) {
                    return $value;
                }
                $rawPayload = $this->raw_payload;
                return $rawPayload['result']['certificate_serial'] ?? null;
            }
        );
    }

    /**
     * Accessor for certificate_url with fallback to raw_payload.
     */
    protected function certificateUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$this->isPassed()) {
                    return null;
                }
                if (!empty($value)) {
                    return $value;
                }
                $rawPayload = $this->raw_payload;
                return $rawPayload['result']['certificate_url'] ?? null;
            }
        );
    }

    /**
     * Accessor for download_certificate with fallback to raw_payload.
     */
    protected function downloadCertificate(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$this->isPassed()) {
                    return null;
                }
                if (!empty($value)) {
                    return $value;
                }
                $rawPayload = $this->raw_payload;
                return $rawPayload['result']['certificate_url'] ?? null;
            }
        );
    }

    /**
     * Accessor for view_results_url with fallback to raw_payload.
     */
    protected function viewResultsUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $url = $value;
                if (empty($url)) {
                    $rawPayload = $this->raw_payload;
                    $url = $rawPayload['result']['view_results_url'] ?? null;
                }
                return !empty($url) ? $url : null;
            }
        );
    }

    /**
     * Get the user that took the exam.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the exam that was taken.
     */
    public function catalogueExam()
    {
        return $this->belongsTo(CatalogueExam::class, 'catalogue_exam_id');
    }
}
