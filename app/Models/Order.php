<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'contact_details', 'comments', 'total_price'];

    public function products()
    {
        return $this->belongsToMany(Product::class)->withTimestamps()->withPivot('product_price');
    }
}
