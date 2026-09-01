<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlugML
{
    protected static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            foreach ($model->getSlugFields() as $field => $slugField) {
                $model->{$slugField} = $model->generateUniqueSlug(null, $field, $slugField);
            }
        });

        static::updating(function ($model) {
            foreach ($model->getSlugFields() as $field => $slugField) {
                if ($model->isDirty($field)) {
                    $model->{$slugField} = $model->generateUniqueSlug($model->id, $field, $slugField);
                }
            }
        });
    }

    protected function generateUniqueSlug($ignoreId = null, $sourceField = 'name', $slugField = 'slug'): string
    {
        $slug = Str::slug($this->{$sourceField});
        $originalSlug = $slug;
        $count = 1;

        while (
            static::where($slugField, $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }

    /**
     * Define which source field maps to which slug field
     * Override this in your model
     */
    protected function getSlugFields(): array
    {
        return [
            'name_en' => 'slug_en',
            'name_bn' => 'slug_bn',
        ];
    }
}


//    Override if you want only specific languages with different fields -> Usage Example:
//    use HasSlugML;
//     protected function getSlugFields(): array
//     {
//         return [
//             'title_en' => 'slug_en',
//             'title_bn' => 'slug_bn',
//         ];
//     }
