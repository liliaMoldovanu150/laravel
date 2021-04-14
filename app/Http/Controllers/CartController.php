<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function show(Request $request)
    {
        $cartProducts = Product::whereIn('id', session('cartProducts') ?? [])->get();
        $totalPrice = 0;
        foreach ($cartProducts as $cartProduct) {
            $totalPrice += $cartProduct->price;
        }

        return $request->wantsJson()
            ? response()->json([
                'cartProducts' => $cartProducts,
                'totalPrice' => $totalPrice
            ])
            : view('cart.show', compact('cartProducts', 'totalPrice'));
    }

    public function store(Product $product, Request $request)
    {
        $ids = session()->get('cartProducts', []);
        session()->put('cartProducts', [...$ids, $product->id]);

        return $request->wantsJson()
            ? response()->json(['success' => true], 200)
            : redirect()->back();
    }

    public function destroy(Product $product, Request $request)
    {
        $key = array_search($product->id, session('cartProducts'));
        $cartProducts = session()->pull('cartProducts', []);
        unset($cartProducts[$key]);
        session()->put('cartProducts', $cartProducts);

        return $request->wantsJson()
            ? response()->json(['success' => true], 200)
            : redirect()->back();
    }
}
