<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Address;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use App\Http\Requests\PurchaseRequest;

class PurchaseController extends Controller
{
    public function index_list()
    {
        $user = Auth::user();

        $purchases = Purchase::with('product.images')
            ->where('buyer_id', $user->id)
            ->latest()
            ->get();

        $sales = Purchase::with('product.images')
            ->where('seller_id', $user->id)
            ->latest()
            ->get();

        return view('mypage.purchase_list', compact('purchases', 'sales'));
    }
/**
     * 商品購入画面
     * GET /purchase/{item_id}
     */
    public function index($item_id)
    {
        $product = Product::with('images')->findOrFail($item_id);

        if ($product->status !== 'selling') {
            return redirect()->route('products.show', $item_id)
                ->with('error', 'この商品は現在購入できません。');
        }

        $user = auth()->user();

        // 初期値は送付先住所 > なければ登録住所
        $address = $user->shippingAddress ?? $user->registeredAddress;

        return view('products.purchase', compact('product', 'address'));
    }

    /**
     * 購入処理
     * POST /purchase/execute/{item_id}
     */
    public function execute(PurchaseRequest $request, $item_id)
    {

        $product = Product::findOrFail($item_id);
    /**
     *  テスト環境では Stripe をスキップして即購入成功とする
     */
        if (app()->environment('testing')) {
            $request->merge([
                'payment_method' => $request->payment_method ?? 'convenience',
                'address_id'     => Address::where('user_id', Auth::id())
                    ->where('type', 'shipping')
                    ->value('id'),
            ]);
            $this->completePurchase($product);
            session()->flash('payment_method', $request->payment_method);

            return redirect('/');
        }

        if ($product->status === 'sold') {
            return back()->with('error', 'この商品は既に購入されています');
        }

        // Stripe 初期化
        Stripe::setApiKey(env('STRIPE_SECRET'));

        if ($request->payment_method === 'convenience') {
            $paymentMethods = ['konbini'];
        } else {
            $paymentMethods = ['card'];
        }

        // Stripe Checkout セッション作成
        $session = StripeSession::create([
            'payment_method_types' => $paymentMethods,
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
            'metadata' => [
                'payment_method_label' => $request->payment_method,
                'product_id'           => $product->id,
                'buyer_id'             => Auth::id(),
            ],
            'success_url' => route('purchase.success', ['item_id' => $product->id]),
            'cancel_url'  => route('purchase.index', ['item_id' => $product->id]),
        ]);

        // Stripe の決済画面へリダイレクト
        return redirect($session->url);
    }

    /**
     * Stripe決済成功後
     * GET /purchase/success/{item_id}
     */
    public function success(Request $request, $item_id)
    {
        $product = Product::findOrFail($item_id);

        // すでに sold なら何もしない
        if ($product->status === 'selling') {
            $purchase = $this->completePurchase($product);
            return redirect()->route('transaction.chat', $purchase->id)
                ->with('success', '購入が完了しました。取引メッセージを送ってみましょう！');
        }

        return redirect('/');
    }

    /**
     * 住所変更ページ
     * GET /purchase/address/{item_id}
     */
    public function addressEdit($item_id)
    {
        $product = Product::findOrFail($item_id);

        $shippingAddress = Address::where('user_id', Auth::id())
            ->where('type', 'shipping')
            ->first();

        $registeredAddress = Address::where('user_id', Auth::id())
            ->where('type', 'registered')
            ->first();

        return view('products.address', [
            'item_id'           => $item_id,
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
     * 共通：購入完了処理（DB 更新）
     */
    private function completePurchase(Product $product)
    {
        $purchase = Purchase::create([
            'buyer_id'   => Auth::id(),
            'seller_id'  => $product->user_id,
            'product_id' => $product->id,
            'status'     => 'shipping_pending',
        ]);

        $product->update(['status' => 'trading']);

        return $purchase;
    }
}
