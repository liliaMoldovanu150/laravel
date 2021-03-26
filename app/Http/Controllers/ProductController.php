<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $cartProducts = session('cartProducts');
        $products = Product::whereNotIn('id', $cartProducts ?? [])->get();
        return view('products.index', compact('products'));
    }

    public function display()
    {
        $products = Product::all();
        return view('products.display', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'price' => 'required',
            'image' => 'required|mimes:jpg,jpeg,png,gif|max:100000'
        ]);

        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension();
        $fileName = time() . '.' . $extension;
        $path = public_path() . '/images';
        $file->move($path, $fileName);

        $newProduct = Product::create($request->all());
        $newProduct->image_url = $fileName;
        $newProduct->save();

        return redirect(route('product.display'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'price' => 'required',
            'image' => 'nullable|mimes:jpg,jpeg,png,gif|max:100000'
        ]);

        Product::where('id', $product->id)
            ->update([
                'title' => $request->title,
                'description' => $request->description,
                'price' => $request->price,
            ]);

        if ($request->file('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '.' . $extension;
            $path = public_path() . '/images';
            $file->move($path, $fileName);

            Product::where('id', $product->id)
                ->update([
                    'image_url' => $fileName
                ]);

        }

        return redirect(route('product.display'));
    }

    public function destroy(Product $product)
    {
        Product::destroy($product->id);

        return redirect()->back();
    }
}
