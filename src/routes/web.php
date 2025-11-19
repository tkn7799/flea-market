<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AddressController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ItemController::class, 'index'])->name('products.index');

Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('products.show');

Route::middleware(['auth'])->group(function () {

    // プロフィール画面
    Route::get('/mypage', [ProfileController::class, 'show'])->name('profile.show');
    // プロフィール編集
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');

    // 商品出品
    Route::get('/sell', [ProductController::class, 'create'])->name('products.create');
    Route::post('/sell', [ProductController::class, 'store'])->name('products.store');

    // マイリスト
    Route::post('/favorite/{item_id}', [FavoriteController::class, 'toggle'])->name('favorite.toggle');

    // コメント
    Route::post('/comment/{item_id}', [CommentController::class, 'store'])->name('comment.store');

    // 購入確認画面
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'index'])->name('purchase.index');

    // 送付先住所変更画面
    Route::get('/purchase/address/{item_id}', [AddressController::class, 'edit'])->name('purchase.address.edit');
    Route::post('/purchase/address/{item_id}', [AddressController::class, 'update'])->name('purchase.address.update');

    // 購入処理
    Route::post('/purchase/execute/{item_id}', [PurchaseController::class, 'purchase'])->name('purchase.execute');

    // 登録住所更新
    Route::post('/mypage/profile/address/registered',
        [AddressController::class, 'updateRegistered'])->name('address.registered.update');

    // 送付先住所更新
    Route::post('/mypage/profile/address/shipping',
        [AddressController::class, 'updateShipping'])->name('address.shipping.update');
});