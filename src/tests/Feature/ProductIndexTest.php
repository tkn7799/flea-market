<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductImage;

class ProductIndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 全商品を取得できる()
    {
        // 商品を3つ作成
        Product::factory()->count(3)->create();

        $response = $this->get('/');

        // 商品名がすべて表示されるか確認
        foreach (Product::all() as $product) {
            $response->assertSee($product->product_name);
        }
    }

    /** @test */
    public function 購入済み商品は_sold_ラベルが表示される()
    {
        // sold 状態の商品を作成
        $user = User::factory()->create();

        $soldProduct = Product::factory()->create([
            'status' => 'sold',
            'product_name' => '売れた商品',
        ]);

        $response = $this->get('/');

        // Sold のラベルが表示されること
        $response->assertSee('Sold');
    }

    /** @test */
    public function 自分が出品した商品は一覧に表示されない()
    {
        $user = User::factory()->create();

        // ログイン
        $this->actingAs($user);

        // 自分の商品（本来は非表示）
        $myProduct = Product::factory()->create([
            'user_id' => $user->id,
            'product_name' => '自分の商品',
        ]);

        // 他人の商品（表示される）
        $otherProduct = Product::factory()->create([
            'product_name' => '他人の商品',
        ]);

        $response = $this->get('/');

        // 自分の商品が表示されないこと
        $response->assertDontSee('自分の商品');

        // 他人の商品は表示される
        $response->assertSee('他人の商品');
    }
}