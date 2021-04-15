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
        $ids = session()->get('cartProducts', []);
        session()->put('cartProducts', [...$ids, $product->id]);

        return response()->json(session()->get('cartProducts'));
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
