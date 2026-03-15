<?php

namespace App\Http\Controllers;

use App\Models\TransactionMessage;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\TransactionMessageRequest;

class TransactionMessageController extends Controller
{
    /**
     * 取引チャット画面の表示
     */
    public function index(Purchase $purchase)
    {
        // 取引の当事者（購入者か出品者）以外はアクセス不可
        if (Auth::id() !== $purchase->buyer_id && Auth::id() !== $purchase->seller_id) {
            abort(403);
        }

        $purchase->messages()
            ->where('user_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = TransactionMessage::where('purchase_id', $purchase->id)
            ->with('user')
            ->oldest()
            ->get();

        $otherPurchases = Purchase::where('id', '!=', $purchase->id)
            ->where(function($query) {
                $query->where('buyer_id', Auth::id())
                    ->orWhere('seller_id', Auth::id());
            })
            ->where('status', '!=', 'completed')
            ->latest()
            ->take(10)
            ->get();

        return view('transactions.chat', compact('purchase', 'messages', 'otherPurchases'));
    }

    /**
     * メッセージの送信
     */
    public function store(TransactionMessageRequest $request, Purchase $purchase)
    {
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('transaction_messages', 'public');
        }

        $purchase->messages()->create([
            'user_id' => Auth::id(),
            'message' => $request->message,
            'image_path' => $imagePath,
        ]);

        return back()->with('success', 'メッセージを送信しました。');
    }

    /**
     * メッセージの削除
     */
    public function destroy(Purchase $purchase, TransactionMessage $message)
    {
        // 自分のメッセージ以外は削除不可
        if (Auth::id() !== $message->user_id) {
            abort(403);
        }

        if ($message->image_path) {
            Storage::disk('public')->delete($message->image_path);
        }

        $message->delete();

        return back()->with('success', 'メッセージを削除しました。');
    }

    public function update(Request $request, Purchase $purchase, TransactionMessage $message)
    {
        // 自分のメッセージ以外は編集不可
        if ($message->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message->update([
            'message' => $request->message
        ]);

        return back()->with('success', 'メッセージを更新しました');
    }
}