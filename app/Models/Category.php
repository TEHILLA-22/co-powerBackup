<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'icon',
        'display_order',
        'is_active',
        'is_featured',
        'seo',
    ];

    protected $casts = [
        'seo' => 'json',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getFullPathAttribute()
    {
        $path = collect();
        $category = $this;
        while ($category) {
            $path->prepend($category->name);
            $category = $category->parent;
        }
        return $path->implode(' > ');
    }
}