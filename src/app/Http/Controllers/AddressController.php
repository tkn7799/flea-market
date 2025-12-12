<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Address;
use App\Models\Product;
use App\Http\Requests\AddressRequest;

class AddressController extends Controller
{
    /**
     * 購入画面 → 住所変更ページ表示
     */
    public function edit($itemId)
    {
        $product = Product::findOrFail($itemId);

        // 登録済み住所（type = registered）
        $registeredAddress = Address::where('user_id', Auth::id())
            ->where('type', 'registered')
            ->first();

        $shippingAddress = Address::where('user_id', Auth::id())
            ->where('type', 'shipping')
            ->first();

        return view('products.address', compact(
            'itemId',
            'product',
            'registeredAddress',
            'shippingAddress'
        ));
    }

    /**
     * 購入画面 → 住所変更処理
     */
    public function update(AddressRequest $request, $itemId)
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
            ->with('success', '送付先住所を更新しました');
    }

    /**
     * マイページ：登録住所の更新
     */
    public function updateRegistered(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'postal_code' => 'required|string|max:10',
            'address'     => 'required|string|max:255',
            'building'    => 'nullable|string|max:255',
        ]);

        Address::updateOrCreate(
            ['user_id' => $user->id, 'type' => 'registered'],
            [
                'postal_code' => $request->postal_code,
                'address'     => $request->address,
                'building'    => $request->building,
            ]
        );

        return back()->with('success', '登録住所を更新しました');
    }

    /**
     * マイページ：送付先住所の更新
     */
    public function updateShipping(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'postal_code' => 'required|string|max:10',
            'address'     => 'required|string|max:255',
            'building'    => 'nullable|string|max:255',
        ]);

        Address::updateOrCreate(
            ['user_id' => $user->id, 'type' => 'shipping'],
            [
                'postal_code' => $request->postal_code,
                'address'     => $request->address,
                'building'    => $request->building,
            ]
        );

        return back()->with('success', '送付先住所を更新しました');
    }

    public function addressEdit($itemId)
    {
        $product = Product::findOrFail($itemId);

        $registeredAddress = Address::where('user_id', Auth::id())
            ->where('type', 'registered')
            ->first();

        $shippingAddress = Address::where('user_id', Auth::id())
            ->where('type', 'shipping')
            ->first();

        return view('products.address', compact(
            'itemId',
            'product',
            'registeredAddress',
            'shippingAddress'
        ));
    }
}
