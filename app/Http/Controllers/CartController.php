<?php

namespace App\Http\Controllers;

use App\Models\Product;

class CartController extends Controller
{
    public function show()
    {
        $cartProducts = Product::whereIn('id', session('cartProducts') ?? [])->get();
        $totalPrice = 0;
        foreach ($cartProducts as $cartProduct) {
            $totalPrice += $cartProduct->price;
        }
        return view('cart.show', compact('cartProducts', 'totalPrice'));
    }

    public function store(Product $product)
    {
        if (Product::findOrFail($product->id)) {
            if (session()->exists('cartProducts')) {
                session()->push('cartProducts', $product->id);
            } else {
                session()->put('cartProducts', [$product->id]);
            }
        }
        return redirect()->back();
    }

    public function destroy(Product $product)
    {
        $key = array_search($product->id, session('cartProducts'));
        $cartProducts = session()->pull('cartProducts', []);
        unset($cartProducts[$key]);
        session()->put('cartProducts', $cartProducts);
        return redirect()->back();
    }
}
