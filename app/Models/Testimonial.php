<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'content',
        'rating',
        'approved',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
