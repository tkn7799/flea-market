<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ItemController extends Controller
{
    public function index()
    {
        $products = Product::with('images')->latest()->get();

        return view('products.index', compact('products'));
    }

    public function show($id)
    {
        $product = Product::with(['images', 'category', 'user', 'comments.user'])->findOrFail($id);

        return view('products.detail', compact('product'));
    }
}
