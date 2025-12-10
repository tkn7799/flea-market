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

        // 初期値は送付先住所 > なければ登録住所
        $address = $user->shippingAddress ?? $user->registeredAddress;

        return view('products.purchase', compact('product', 'address'));
    }

    /**
     * 購入処理
     * POST /purchase/execute/{item_id}
     */
    public function execute(Request $request, $item_id)
    {

        $product = Product::findOrFail($item_id);
    /**
     *  テスト環境では Stripe をスキップして即購入成功とする
     */
        if (app()->environment('testing')) {
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
        if ($product->status !== 'sold') {
            $this->completePurchase($product);
        }

        return redirect('/')
            ->with('success', '購入が完了しました');
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
        Purchase::create([
            'buyer_id'   => Auth::id(),
            'seller_id'  => $product->user_id,
            'product_id' => $product->id,
        ]);

        $product->status = 'sold';
        $product->save();
    }
}
