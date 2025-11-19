<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Address;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $registeredAddress = $user->registeredAddress;
        $shippingAddress = $user->shippingAddress;

        return view('profile.edit', compact('user', 'registeredAddress', 'shippingAddress'));
    }
    public function update(Request $request)
    {
    $user = Auth::user();

    $request->validate([
        'user_name' => 'required|string|max:255',
        'postal_code' => 'required|string|max:10',
        'address' => 'required|string|max:255',
        'building' => 'nullable|string|max:255',
        'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // プロフィール画像アップロード
    if ($request->hasFile('profile_image')) {
        $path = $request->file('profile_image')->store('profile_images', 'public');
        $user->profile_image = $path;
    }

    $user->user_name = $request->user_name;
    $user->save();

    // 登録住所を更新 or 作成
    Address::updateOrCreate(
        ['user_id' => $user->id, 'type' => 'registered'],
        [
            'postal_code' => $request->postal_code,
            'address' => $request->address,
            'building' => $request->building,
        ]
    );

    return redirect()->route('profile.edit')->with('success', 'プロフィールを更新しました');
    }

    public function show()
    {
        $user = Auth::user();
        $page = request()->query('page');
        $purchased = $user->purchases()->with('product.images')->get();
        $selling = $user->products()->with('images')->get();

        return view('profile.show', compact('user', 'page', 'purchased', 'selling'));
    }
}
