<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectImage extends Model
{
    protected $appends = ['image_url', 'mobile_image_url'];

    protected $fillable = ['project_id', 'image', 'image_mobile', 'focal_point', 'sort_order'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /** Legacy filename in public/images vs admin-uploaded file in storage. Same rules as Project::imageUrl(). */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->resolveImagePath($this->image));
    }

    /** Mobile variant (phones ≤ 767px). Same resolution rules as imageUrl(). */
    protected function mobileImageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->resolveImagePath($this->image_mobile));
    }

    private function resolveImagePath(?string $path): ?string
    {
        if (!$path) {
            return null;
        }
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }
        if (str_starts_with($path, 'uploads/') || str_contains($path, '/')) {
            return asset('storage/' . ltrim($path, '/'));
        }
        return asset('images/' . $path);
    }
}
