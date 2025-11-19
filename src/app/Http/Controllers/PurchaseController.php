<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Address;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    /**
     * 商品購入画面
     * GET /purchase/{item_id}
     */
    public function index($itemId)
    {
        $product = Product::with(['images', 'user'])->findOrFail($itemId);

        // ユーザーの送付先住所
        $shippingAddress = Address::where('user_id', Auth::id())
            ->where('type', 'shipping')
            ->first();

        return view('products.purchase', compact('product', 'shippingAddress'));
    }

    /**
     * 購入処理
     * POST /purchase/{item_id}/complete
     */
    public function complete(Request $request, $itemId)
    {
        $request->validate([
            'payment_method' => 'required|string',
        ]);

        $product = Product::findOrFail($itemId);

        if ($product->status === 'sold') {
            return back()->with('error', 'この商品は既に購入されています');
        }

        // 購入履歴登録
        Purchase::create([
            'buyer_id'  => Auth::id(),
            'seller_id' => $product->user_id,
            'product_id'=> $product->id,
        ]);

        // ステータス変更
        $product->status = 'sold';
        $product->save();

        return redirect('/')->with('success', '購入が完了しました');
    }

    /**
     * 住所変更ページ
     * GET /purchase/address/{item_id}
     */
    public function addressEdit($itemId)
    {
        $product = Product::findOrFail($itemId);

        $shippingAddress = Address::where('user_id', Auth::id())
            ->where('type', 'shipping')
            ->first();

        return view('products.address', compact('product', 'shippingAddress'));
    }

    /**
     * 住所更新処理
     * POST /purchase/address/{item_id}
     */
    public function addressUpdate(Request $request, $itemId)
    {
        $request->validate([
            'postal_code' => 'required|string|max:10',
            'address'     => 'required|string|max:255',
            'building'    => 'nullable|string|max:255',
        ]);

        Address::updateOrCreate(
            ['user_id' => Auth::id(), 'type' => 'shipping'],
            [
                'postal_code' => $request->postal_code,
                'address'     => $request->address,
                'building'    => $request->building,
            ]
        );

        return redirect()->route('purchase.index', ['item_id' => $itemId])
            ->with('success', '住所を更新しました');
    }
}
