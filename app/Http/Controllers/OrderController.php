<?php

namespace App\Http\Controllers;

use App\Mail\Email;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'details' => 'required',
            'comments' => 'nullable',
        ]);

        $newOrder = new Order();
        $newOrder->name = $request->name;
        $newOrder->contact_details = $request->details;
        $newOrder->comments = $request->comments ?? '';
        $newOrder->total_price = $request->totalPrice;
        $newOrder->save();

        $cartProducts = session()->get('cartProducts');

        foreach ($cartProducts as $cartProduct) {
            $newOrder->products()->attach([$cartProduct => ['product_price' => Product::find($cartProduct)->price]]);
        }

        $orderProducts = $newOrder->products->toArray();

        Mail::to(config('mail.manager_email'))
            ->send(new Email($newOrder, $orderProducts));

        session()->forget('cartProducts');

        if ($request->ajax()) {
            return response()->json(['statusCode' => 200]);
        } else {
            return redirect(route('product.index'));
        }
    }

    public function index()
    {
        $orders = Order::with('products')->get();
        return view('orders.index', compact('orders'));
    }
}
