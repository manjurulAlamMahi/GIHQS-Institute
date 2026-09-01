<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    /**
     * Laravel automatically calls this when trait is used in model
     */
    protected static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            $model->slug = $model->generateUniqueSlug();
        });

        static::updating(function ($model) {
            if ($model->isDirty($model->getSlugSource())) {
                $model->slug = $model->generateUniqueSlug($model->id);
            }
        });
    }

    /**
     * Generate unique slug
     */
    protected function generateUniqueSlug($ignoreId = null): string
    {
        $source = $this->getSlugSource(); // name / title
        $slugColumn = $this->getSlugColumn(); // slug

        $slug = Str::slug($this->{$source});
        $originalSlug = $slug;
        $count = 1;

        while (
            static::where($slugColumn, $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }

    /**
     * Which field slug is generated from
     */
    protected function getSlugSource(): string
    {
        return 'name'; // default
    }

    /**
     * Slug column name
     */
    protected function getSlugColumn(): string
    {
        return 'slug';
    }
}


    // for slug generation, source field is 'name' by default from HasSlug trait
    // but we want to use 'title' field for this model then override the method -> Usage Example:
    // use HasSlug;
    // protected function getSlugSource(): string
    // {
    //     return 'title';
    // }
