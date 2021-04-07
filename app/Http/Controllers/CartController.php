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

        if ($request->ajax()) {
            return response()->json([
                'cartProducts' => $cartProducts,
                'totalPrice' => $totalPrice
            ]);
        } else {
            return view('cart.show', compact('cartProducts', 'totalPrice'));
        }
    }

    public function store(Product $product, Request $request)
    {
        if (Product::find($product->id)) {
            if (session()->exists('cartProducts')) {
                session()->push('cartProducts', $product->id);
            } else {
                session()->put('cartProducts', [$product->id]);
            }
        }

        if ($request->ajax()) {
            return response()->json(['success' => true], 200);
        } else {
            return redirect()->back();
        }
    }

    public function destroy(Product $product, Request $request)
    {
        $key = array_search($product->id, session('cartProducts'));
        $cartProducts = session()->pull('cartProducts', []);
        unset($cartProducts[$key]);
        session()->put('cartProducts', $cartProducts);

        if ($request->ajax()) {
            return response()->json(['success' => true], 200);
        } else {
            return redirect()->back();
        }
    }
}
