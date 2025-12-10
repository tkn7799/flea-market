<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductPurchaseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ① 「購入する」ボタン押下すると購入が完了する
     */
    public function test_購入手続きが完了する()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create([
            'status' => 'selling',
        ]);

        // 購入実行
        $response = $this->post("/purchase/execute/{$product->id}");

        // 成功画面へリダイレクト
        $response->assertRedirect("/");

        // 商品ステータスが sold に更新されている
        $this->assertDatabaseHas('products', [
            'id'     => $product->id,
            'status' => 'sold',
        ]);
    }

    /**
     * ② 購入した商品は商品一覧画面で「sold」と表示される
     */
    public function test_購入済み商品に_sold_ラベルが表示される()
    {
        $user = User::factory()->create();

        $product = Product::factory()->create([
            'status' => 'sold',
            'product_name' => 'テスト商品',
        ]);

        // 一覧ページ表示
        $response = $this->get('/');

        // SOLD 表示があるか
        $response->assertSee('Sold');
        $response->assertSee('テスト商品');
    }

    /**
     * ③ 購入した商品がプロフィールの「購入した商品一覧」に追加されている
     */
    public function test_購入した商品がプロフィール画面に追加される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create([
            'user_id' => User::factory()->create()->id,
            'status' => 'selling'
        ]);

        // 購入実行
        Purchase::create([
            'buyer_id'   => $user->id,
            'seller_id'  => $product->user_id,
            'product_id' => $product->id,
        ]);

        // プロダクトのステータスも変更
        $product->update(['status' => 'sold']);

        // 「購入した商品」タブを開く
        $response = $this->get('/mypage?page=buy');

        // 商品名が表示されている
        $response->assertSee($product->product_name);
    }
}
