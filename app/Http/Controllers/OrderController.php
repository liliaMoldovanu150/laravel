<?php

namespace App\Http\Controllers;

use App\Mail\Email;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('products')->get();

        return $request->wantsJson()
            ? response()->json($orders)
            : view('orders.index', compact('orders'));
    }

    public function store(Request $request)
    {
        $orderValues = $request->validate([
            'name' => 'required',
            'contact_details' => 'required',
            'comments' => 'nullable',
        ]);

        $cartProducts = session()->get('cartProducts');
        $orderPrice = Product::whereIn('id', $cartProducts)->pluck('price')->sum();
        $orderValues['total_price'] = $orderPrice;

        $newOrder = Order::create($orderValues);

        foreach ($cartProducts as $cartProduct) {
            $newOrder->products()->attach([$cartProduct => ['product_price' => Product::find($cartProduct)->price]]);
        }

        $orderProducts = $newOrder->products;

        Mail::to(config('mail.manager_email'))
            ->send(new Email($newOrder, $orderProducts));

        session()->forget('cartProducts');

        return $request->wantsJson()
            ? response()->json(['success' => true], 200)
            : redirect(route('product.index'));
    }

    public function show(Order $order, Request $request)
    {
        $order = Order::with('products')->find($order->id);
        $orderProducts = $order->products;

        return $request->wantsJson()
            ? response()->json([
                'order' => $order,
                'orderProducts' => $orderProducts
            ])
            : view('orders.show', compact('order'));
    }
}
