<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    /**
     * マイリスト追加・削除（トグル動作）
     * パス: POST /favorite/{product_id}
     */
    public function toggle($productId)
    {
        $userId = Auth::id();

        // 既にマイリスト登録されているか確認
        $favorite = Favorite::where('user_id', $userId)
                            ->where('product_id', $productId)
                            ->first();

        if ($favorite) {
            // 削除
            $favorite->delete();

            return response()->json([
                'status' => 'removed',
                'message' => 'マイリストから削除しました',
            ]);
        } else {
            // 追加
            Favorite::create([
                'user_id'    => $userId,
                'product_id' => $productId,
            ]);

            return response()->json([
                'status' => 'added',
                'message' => 'マイリストに追加しました',
            ]);
        }
    }
}
