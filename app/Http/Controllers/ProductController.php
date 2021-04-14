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

        return $request->wantsJson()
            ? response()->json($products)
            : view('products.index', compact('products'));
    }

    public function display(Request $request)
    {
        $products = Product::all();

        return $request->wantsJson()
            ? response()->json($products)
            : view('products.display', compact('products'));
    }

    public function create(Request $request)
    {
        return $request->wantsJson()
            ? response()->json(['success' => true], 200)
            : view('products.create');
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
        $productValues = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'price' => 'required',
            'image' => 'required|mimes:jpg,jpeg,png,gif|max:100000'
        ]);

        $productValues['image_url'] = $this->uploadImage($request);
        Product::create($productValues);

        return $request->wantsJson()
            ? response()->json(['success' => true], 200)
            : redirect(route('product.display'));
    }

    public function edit(Product $product, Request $request)
    {
        return $request->wantsJson()
            ? response()->json($product)
            : view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $productValues = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'price' => 'required',
            'image' => 'nullable|mimes:jpg,jpeg,png,gif|max:100000'
        ]);

        $product->update($productValues);

        if ($request->file('image')) {
            $product->update([
                'image_url' => $this->uploadImage($request)
            ]);
        }

        return $request->wantsJson()
            ? response()->json(['success' => true], 200)
            : redirect(route('product.display'));
    }

    public function destroy(Product $product, Request $request)
    {
        File::delete('./images/' .$product->image_url);
        Product::destroy($product->id);

        return $request->wantsJson()
            ? response()->json(['success' => true], 200)
            : redirect()->back();
    }
}


