<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    protected $casts = [
        'images' => 'array',
        'is_featured' => 'boolean',
        'is_new' => 'boolean',
        'is_best_seller' => 'boolean',
        'is_limited_edition' => 'boolean',
        'in_stock' => 'boolean',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Recompute the denormalized average rating and review count from the
     * currently APPROVED reviews. Called automatically whenever a review is
     * added, edited, approved, rejected, or deleted.
     */
    public function recalculateRating(): void
    {
        $approved = $this->reviews()
            ->where('status', Review::STATUS_APPROVED)
            ->get(['rating']);

        $this->reviews_count = $approved->count();
        $this->average_rating = $approved->isEmpty()
            ? 0
            : round((float) $approved->avg('rating'), 2);

        $this->saveQuietly();
    }
}
