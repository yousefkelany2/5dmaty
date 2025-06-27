<?php

namespace App\Models;

use App\Models\Service;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'service_id',
        'name',
        'phone',
        'email',
        'status',
    ];
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
