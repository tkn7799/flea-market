<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;

class CommentController extends Controller
{
    /**
     * コメント投稿
     * パス: POST /item/{item_id}/comment
     */
    public function store(Request $request, $productId)
    {
        // バリデーション
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        // コメント保存
        Comment::create([
            'user_id'    => Auth::id(),
            'product_id' => $productId,
            'comment'    => $request->comment,
        ]);

        return redirect()->back()->with('success', 'コメントを投稿しました！');
    }
}
