<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Address;
use App\Models\Purchase;

class ProfileController extends Controller
{
    /**
     * マイページ表示
     * GET /mypage
     */
    public function show()
    {
        $user = Auth::user();
        $page = request()->query('page', 'sell'); // タブ切替

        // 出品商品
        $selling = $user->products()->with('images')->get();

        // 購入商品
        $purchased = Purchase::with('product.images')
            ->where('buyer_id', Auth::id())
            ->get();

        return view('profile.show', compact('user', 'page', 'selling', 'purchased'));
    }

    /**
     * プロフィール編集画面
     * GET /mypage/profile
     */
    public function edit()
    {
        $user = Auth::user();

        // 登録住所 (type = registered)
        $registeredAddress = Address::where('user_id', $user->id)
            ->where('type', 'registered')
            ->first();

        return view('profile.edit', compact('user', 'registeredAddress'));
    }

    /**
     * プロフィール更新処理
     * PUT /mypage/profile
     */
    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        // -----------------------
        // 画像保存処理
        // -----------------------
        if ($request->hasFile('profile_image')) {

            // 旧画像削除
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            // 新規画像を保存
            $path = $request->profile_image->store('profile_images', 'public');

            $user->profile_image = $path;
        }

        // -----------------------
        // ユーザー名更新
        // -----------------------
        $user->user_name = $request->user_name;
        $user->save();

        // -----------------------
        // 登録住所更新
        // -----------------------
        Address::updateOrCreate(
            ['user_id' => $user->id, 'type' => 'registered'],
            [
                'postal_code' => $request->postal_code,
                'address'     => $request->address,
                'building'    => $request->building,
            ]
        );

        return redirect()->route('profile.show')->with('success', 'プロフィールを更新しました！');
    }
}
