<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatalogueHtmlResource extends Model
{
    protected $table = 'catalogue_html_resources';

    /** Recognised values for the `kind` label. Does not affect rendering. */
    public const KINDS = ['module', 'story_guide', 'toolkit', 'worksheet'];

    protected $fillable = [
        'catalogue_id',
        'title',
        'kind',
        'file_path',
        'is_public',
        'sort_order',
        'access_key',
        'license_validity_days',
    ];

    protected $casts = [
        'is_public'             => 'boolean',
        'sort_order'            => 'integer',
        'license_validity_days' => 'integer',
    ];

    /** The access key must never travel to the client. */
    protected $hidden = ['access_key'];

    public function catalogue(): BelongsTo
    {
        return $this->belongsTo(Catalogue::class);
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(HtmlResourceLicense::class, 'catalogue_html_resource_id');
    }

    /**
     * A resource is licence-gated only when an admin has set an access key on it.
     */
    public function requiresLicense(): bool
    {
        return filled($this->access_key);
    }

    /**
     * Absolute path of the stored document, or null when the file is gone.
     */
    public function absolutePath(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        $path = public_path($this->file_path);

        return is_file($path) ? $path : null;
    }
}
