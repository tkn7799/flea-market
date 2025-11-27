<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Address;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class PurchaseController extends Controller
{
    /**
     * 商品購入画面
     * GET /purchase/{item_id}
     */
    public function index($item_id)
    {
        $product = Product::with('images')->findOrFail($item_id);

         $user = auth()->user();

        // 初期値は登録住所
        $address = $user->shippingAddress ?? $user->registeredAddress;

        return view('products.purchase', compact('product', 'address'));
    }

    /**
     * 購入処理
     * POST /purchase/execute/{item_id}
     */
    public function execute(Request $request, $itemId)
    {
        $request->validate([
            'payment_method' => 'required|string',
        ]);

        $product = Product::findOrFail($itemId);

        if ($product->status === 'sold') {
            return back()->with('error', 'この商品は既に購入されています');
        }

        // === コンビニ払いの場合（デモのため即購入完了扱い）===
        if ($request->payment_method === 'convenience') {

            $this->completePurchase($product);

            return redirect('/')
                ->with('success', 'コンビニ支払いで購入が完了しました');
        }

        // === クレジットカード払い（Stripe） ===
        if ($request->payment_method === 'card') {

            Stripe::setApiKey(env('STRIPE_SECRET'));

            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => [
                            'name' => $product->product_name,
                        ],
                        'unit_amount' => $product->price,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('purchase.success', ['item_id' => $product->id]),
                'cancel_url' => route('purchase.index', ['item_id' => $product->id]),
            ]);

            return redirect($session->url);
        }

        return back()->with('error', '決済方法が不正です');
    }


    /**
     * Stripe決済成功後
     * GET /purchase/success/{item_id}
     */
    public function success(Request $request, $itemId)
    {
        $product = Product::findOrFail($itemId);

        $this->completePurchase($product);

        return redirect('/')
            ->with('success', 'カード支払いで購入が完了しました');
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

        $registeredAddress = Address::where('user_id', Auth::id())
            ->where('type', 'registered')
            ->first();

        return view('products.address', [
            'item_id'           => $itemId,
            'product'           => $product,
            'shippingAddress'   => $shippingAddress,
            'registeredAddress' => $registeredAddress,
        ]);
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


    /**
     * 共通：購入完了処理
     */
    private function completePurchase($product)
    {
        // 購入履歴作成
        Purchase::create([
            'buyer_id'  => Auth::id(),
            'seller_id' => $product->user_id,
            'product_id'=> $product->id,
        ]);

        // 商品ステータス更新
        $product->status = 'sold';
        $product->save();
    }
}
