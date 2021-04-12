<?php

namespace App\Http\Controllers;

use App\Mail\Email;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('products')->get();
        return view('orders.index', compact('orders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'contact_details' => 'required',
            'comments' => 'nullable',
        ]);

        $newOrder = Order::create($request->all());
        $newOrder->save();

        $cartProducts = session()->get('cartProducts');

        foreach ($cartProducts as $cartProduct) {
            $newOrder->products()->attach([$cartProduct => ['product_price' => Product::find($cartProduct)->price]]);
        }

        $orderProducts = $newOrder->products->toArray();

        Mail::to(config('mail.manager_email'))
            ->send(new Email($newOrder, $orderProducts));

        session()->forget('cartProducts');

        return redirect(route('product.index'));
    }

    public function show(Order $order)
    {
        $order = Order::with('products')->find($order->id);
        return view('orders.show', compact('order'));
    }
}
