<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'price', 'image_url'];

    public function orders()
    {
        return $this->belongsToMany(Order::class)->withTimestamps()->withPivot('product_price');
    }
}
