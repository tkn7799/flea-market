<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;

class ProductController extends Controller
{
    /**
     * 商品出品画面（GET）
     * パス: /sell
     */
    public function create()
    {
        $categories = Category::all(); // カテゴリ一覧取得

        return view('products.listing', compact('categories'));
    }

    /**
     * 商品を保存する（POST）
     * パス: /sell
     */
    public function store(Request $request)
    {
        // バリデーション
        $validated = $request->validate([
            'product_name'  => 'required|string|max:255',
            'brand_name'    => 'nullable|string|max:255',
            'condition'     => 'required|string',
            'description'   => 'required|string',
            'price'         => 'required|integer|min:1',
            'images.*'      => 'image|mimes:jpg,jpeg,png|max:2048',
            'categories'    => 'required|array',
        ]);

        // 商品データを保存
        $product = Product::create([
            'user_id'      => Auth::id(),
            'product_name' => $request->product_name,
            'brand_name'   => $request->brand_name,
            'condition'    => $request->condition,
            'description'  => $request->description,
            'price'        => $request->price,
            'status'       => 'selling',  // 初期状態 = 出品中
        ]);

        // カテゴリ（多対多）を保存
        $product->categories()->attach($request->categories);

        // 画像保存
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {

                $path = $imageFile->store('product_images', 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                ]);
            }
        }

        return redirect()->route('products.show', $product->id)
                         ->with('success', '商品を出品しました！');
    }
}
