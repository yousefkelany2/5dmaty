<?php

namespace App\Models;

use App\Models\Order;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'category_id',
        'name_ar',
        'name_en',
        'price',
        'description_ar',
        'description_en',
        'image',
        'tags',
    ];
    protected $casts = [
        'tags' => 'array',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
     protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
