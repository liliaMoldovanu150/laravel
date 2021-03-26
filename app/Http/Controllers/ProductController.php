<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $cartProducts = session('cartProducts');
        $products = Product::whereNotIn('id', $cartProducts ?? [])->get();
        return view('products.index', compact('products'));
    }
}
