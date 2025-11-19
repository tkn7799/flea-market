<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * 登録住所を更新（プロフィール用）
     * POST /mypage/profile/address/registered
     */
    public function updateRegistered(Request $request)
    {
        $request->validate([
            'postal_code' => 'required|string|max:10',
            'address'     => 'required|string|max:255',
            'building'    => 'nullable|string|max:255',
        ]);

        Address::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'type'    => 'registered',
            ],
            [
                'postal_code' => $request->postal_code,
                'address'     => $request->address,
                'building'    => $request->building,
            ]
        );

        return redirect()->route('profile.edit')
            ->with('success', '登録住所を更新しました。');
    }

    /**
     * 送付先住所を更新（プロフィール用）
     * POST /mypage/profile/address/shipping
     */
    public function updateShipping(Request $request)
    {
        $request->validate([
            'postal_code' => 'required|string|max:10',
            'address'     => 'required|string|max:255',
            'building'    => 'nullable|string|max:255',
        ]);

        Address::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'type'    => 'shipping',
            ],
            [
                'postal_code' => $request->postal_code,
                'address'     => $request->address,
                'building'    => $request->building,
            ]
        );

        return redirect()->route('profile.edit')
            ->with('success', '送付先住所を更新しました。');
    }
}
