<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Purchase;
use App\Mail\RatingNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    /**
     * 評価登録処理 (FN012, FN013, FN014)
     */
    public function store(Request $request, Purchase $purchase)
    {
        // 1. バリデーション
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        // 2. 取引の当事者かチェック
        $myId = Auth::id();
        if ($myId !== $purchase->buyer_id && $myId !== $purchase->seller_id) {
            abort(403);
        }

        // 3. 相手のIDを特定
        $toUserId = ($myId === $purchase->buyer_id) ? $purchase->seller_id : $purchase->buyer_id;

        // DBトランザクション開始（評価保存とステータス更新をセットで行うため）
        DB::transaction(function () use ($request, $purchase, $myId, $toUserId) {

            // 4. 評価を保存 (FN012, FN013)
            Rating::updateOrCreate(
                ['purchase_id' => $purchase->id, 'from_user_id' => $myId],
                [
                    'to_user_id' => $toUserId,
                    'rating' => $request->rating,
                ]
            );

            $isOpponentRated = Rating::where('purchase_id', $purchase->id)
                ->where('from_user_id', $toUserId)
                ->exists();

            // 5. 取引ステータスの更新 (FN014)
            // 例：双方が評価し終えたら完了とする、または一方が評価したら次のフェーズへ
            // ここでは簡易的に、評価が行われたら商品ステータスを売却済にする例
            if ($isOpponentRated) {
                $purchase->update(['status' => 'completed']);
                $purchase->product->update(['status' => 'sold']);
            }

            $toUser = \App\Models\User::find($toUserId);
            $fromUser = Auth::user();

            Mail::to($toUser->email)->send(new RatingNotification($purchase, $fromUser));
        });

        // 6. 完了後の遷移 (FN014)
        if ($purchase->fresh()->status !== 'completed') {
            return redirect()->route('transaction.chat', $purchase->id)
                ->with('success', '評価を送信しました。相手の評価が終わると取引完了となります。');
        }

        return redirect()->route('products.index')->with('success', '評価を送信し、取引が完了しました。');
    }
}
