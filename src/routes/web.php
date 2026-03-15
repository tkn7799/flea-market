<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\TransactionMessageController;
use App\Http\Controllers\RatingController;


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

    // マイリスト
Route::post('/favorite/{item_id}', [FavoriteController::class, 'toggle'])->name('favorite.toggle');

    // メール認証を促す画面
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->name('verification.notice');

    // 認証リンククリック時の処理
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/mypage/profile')->with('verified', true);
})->middleware(['signed'])->name('verification.verify');

    // 認証メールの再送
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', '認証メールを再送しました');
})->middleware(['throttle:6,1'])->name('verification.send');

Route::middleware(['auth', 'verified'])->group(function () {
    // プロフィール画面
    Route::get('/mypage', [ProfileController::class, 'show'])->name('profile.show');
    // プロフィール編集
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/mypage/purchases', [PurchaseController::class, 'index_list'])->name('purchase.list');

    // 商品出品
    Route::get('/sell', [ProductController::class, 'create'])->name('products.create');
    Route::post('/sell', [ProductController::class, 'store'])->name('products.store');

    // コメント
    Route::post('/comment/{item_id}', [CommentController::class, 'store'])->name('comment.store');

    // 購入確認画面
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'index'])->name('purchase.index');

    // 送付先住所変更画面
    Route::get('/purchase/address/{item_id}', [AddressController::class, 'edit'])->name('purchase.address.edit');
    Route::post('/purchase/address/{item_id}', [AddressController::class, 'update'])->name('purchase.address.update');

    // 購入処理
    Route::post('/purchase/execute/{item_id}', [PurchaseController::class, 'execute'])->name('purchase.execute');

    Route::get('/purchase/success/{item_id}', [PurchaseController::class, 'success'])->name('purchase.success');

    Route::prefix('transaction/{purchase}')->group(function () {
        // 取引チャット
        Route::get('/chat', [TransactionMessageController::class, 'index'])->name('transaction.chat');
        Route::post('/message', [TransactionMessageController::class, 'store'])->name('transaction.message.store');
        Route::patch('/message/{message}', [TransactionMessageController::class, 'update'])->name('transaction.message.update');
        Route::delete('/message/{message}', [TransactionMessageController::class, 'destroy'])->name('transaction.message.destroy');
    });

        // 評価
        Route::post('/transaction/{purchase}/rating', [RatingController::class, 'store'])->name('rating.store');

    // 登録住所更新
    Route::post('/mypage/profile/address/registered',
        [AddressController::class, 'updateRegistered'])->name('address.registered.update');

    // 送付先住所更新
    Route::post('/mypage/profile/address/shipping',
        [AddressController::class, 'updateShipping'])->name('address.shipping.update');
});