<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $cartProducts = session('cartProducts');
        $products = Product::whereNotIn('id', $cartProducts ?? [])->get();

        if ($request->ajax()) {
            return response()->json($products);
        }
        else {
            return view('products.index', compact('products'));
        }

//        return view('app');
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

    private function uploadImage(Request $request)
    {
        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension();
        $fileName = time() . '.' . $extension;
        $path = public_path() . '/images';
        $file->move($path, $fileName);

        return $fileName;
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'price' => 'required',
            'image' => 'required|mimes:jpg,jpeg,png,gif|max:100000'
        ]);

        $newProduct = Product::create($request->all());
        $newProduct->image_url = $this->uploadImage($request);
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
            Product::where('id', $product->id)
                ->update([
                    'image_url' => $this->uploadImage($request)
                ]);

        }

        return redirect(route('product.display'));
    }

    public function destroy(Product $product)
    {
        File::delete('./images/' .$product->image_url);
        Product::destroy($product->id);

        return redirect()->back();
    }
}
