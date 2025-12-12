<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Favorite;
use App\Models\ProductImage;

class MyListIndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ① いいねした商品だけが表示される
     * ユーザーがいいねした商品だけがマイリストに表示される
     */
    public function test_いいねした商品だけが表示される()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        // 他人の商品2つ
        $product1 = Product::factory()->create(['product_name' => 'PRODUCT_A']);

        // product1 だけお気に入り登録
        Favorite::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product1->id
        ]);

        $product2 = Product::factory()->create(['product_name' => 'PRODUCT_B']);

        $response = $this->get('/?tab=mylist');

        // product1 は表示
        $response->assertSeeText($product1->product_name);

        // product2 は非表示
        $response->assertDontSee($product2->product_name);
    }

    /**
     * ② 購入済み商品には SOLD ラベルが表示される
     */
    public function test_購入済み商品は_sold_ラベルが表示される()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        // SOLD の商品を作成
        $soldProduct = Product::factory()->create([
            'status' => 'sold'
        ]);

        // お気に入り登録
        Favorite::create([
            'user_id' => $user->id,
            'product_id' => $soldProduct->id
        ]);

        $response = $this->get('/?tab=mylist');

        // SOLD ラベルの表示を確認
        $response->assertSee('Sold');
    }

    /**
     * ③ 未ログインの場合はマイリストは空表示
     */
    public function test_未認証の場合は何も表示されない()
    {
        // ログインしないでアクセス
        $response = $this->get('/?tab=mylist');

        // 商品名などが表示されないか確認（空である）
        $response->assertDontSee('item-card'); // HTML 内の商品カードクラス

        // マイリストタブ自体は表示される想定のため以下もOK
        $response->assertStatus(200);
    }
}
