<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use App\Models\Product;

class CommentController extends Controller
{
    public function store(CommentRequest $request, $item_id)
    {
        $product = Product::findOrFail($item_id);


        // コメント保存
        Comment::create([
            'user_id'    => Auth::id(),
            'product_id' => $product->id,
            'comment'    => $request->comment,
        ]);

        return redirect("/item/{$item_id}")->with('success', 'コメントを投稿しました');
    }
}
