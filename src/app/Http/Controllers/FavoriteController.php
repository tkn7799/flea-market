<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    public function toggle(Request $request, $item_id)
    {
        // 未ログインなら JSON でログイン画面へ誘導
        if (!Auth::check()) {
            return response()->json([
                'status'   => 'unauthenticated',
                'redirect' => route('login'),
            ], 401);
        }

        $user    = Auth::user();
        $product = Product::findOrFail($item_id);

        // 既にお気に入り済みかどうか
        $favorite = Favorite::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($favorite) {
            // 解除
            $favorite->delete();
            $status = 'removed';
        } else {
            // 追加
            Favorite::create([
                'user_id'    => $user->id,
                'product_id' => $product->id,
            ]);
            $status = 'added';
        }

        // 最新のお気に入り数
        $count = $product->favorites()->count();

        // Ajax 用レスポンス
        if ($request->wantsJson()) {
            return response()->json([
                'status' => $status,
                'count'  => $count,
            ]);
        }

        // もし普通にフォームで飛んできた場合の保険
        return back();
    }
}
