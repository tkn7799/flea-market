<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab');
        $keyword = $request->query('keyword');

        $query = Product::with('images')
            ->latest();

        if (!empty($keyword)) {
            $query->where('product_name', 'LIKE', '%' . $keyword . '%');
        }

        if ($tab === 'mylist' && Auth::check()) {
            $userId = Auth::id();
            $query->whereHas('favorites', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }

        if (Auth::check()) {
            $query->where('user_id', '!=', Auth::id());
        }

        if ($tab === 'mylist' && Auth::check()) {
            $userId = Auth::id();
            $query->whereHas('favorites', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }

        $products = $query->paginate(20);

        return view('products.index', [
            'products' => $products,
            'tab' => $tab,
            'keyword' => $keyword,
        ]);
    }

    public function show($item_id)
    {
        $product = Product::with([
            'images',
            'categories',
            'user',
            'comments.user',
        ])->findOrFail($item_id);

        $isFavorite = false;
        if (Auth::check()) {
            $isFavorite = $product->favorites()
                ->where('user_id', Auth::id())
                ->exists();
        }

        return view('products.detail', [
            'product' => $product,
            'isFavorite' => $isFavorite,
        ]);
    }
}
