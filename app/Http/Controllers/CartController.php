<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cartProducts = Product::whereIn('id', session('cartProducts') ?? [])->get();
        $totalPrice = 0;
        foreach ($cartProducts as $cartProduct) {
            $totalPrice += $cartProduct->price;
        }
        return view('cart.index', compact('cartProducts', 'totalPrice'));
    }

    public function store(Product $product, Request $request)
    {
        if (Product::find($product->id)) {
            if ($request->session()->exists('cartProducts')) {
                $request->session()->push('cartProducts', $product->id);
            } else {
                $request->session()->put('cartProducts', [$product->id]);
            }
        }
        return redirect(route('product.index'));
    }

    public function delete(Product $product, Request $request)
    {
        $key = array_search($product->id, session('cartProducts'));
        $cartProducts = $request->session()->pull('cartProducts', []);
        unset($cartProducts[$key]);
        session()->put('cartProducts', $cartProducts);
        return redirect()->back();
    }
}
