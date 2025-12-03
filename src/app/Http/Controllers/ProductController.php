<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;

class ProductController extends Controller
{
    /**
     * 出品画面表示
     * GET /sell
     */
    public function create()
    {
        $categories = Category::all();

        return view('products.listing', compact('categories'));
    }

    /**
     * 出品処理
     * POST /sell
     * ExhibitionRequest を使用
     */
    public function store(ExhibitionRequest $request)
    {
        // -----------------------------
        // 1. 商品保存
        // -----------------------------
        $product = Product::create([
            'user_id'      => Auth::id(),
            'product_name' => $request->product_name,
            'brand_name'   => $request->brand_name,
            'condition'    => $request->condition,
            'description'  => $request->description,
            'price'        => $request->price,
            'status'       => 'selling',
        ]);

        // -----------------------------
        // 2. カテゴリー保存（中間テーブル）
        // -----------------------------
        $product->categories()->attach($request->categories);

        // -----------------------------
        // 3. 画像保存
        // -----------------------------
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $path = $imageFile->store('product_images', 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                ]);
            }
        }

        // -----------------------------
        // 4. 商品詳細ページへ遷移
        // -----------------------------
        return redirect()->route('products.show', $product->id)
            ->with('success', '商品を出品しました！');
    }
}
