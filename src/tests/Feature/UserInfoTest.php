<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;

class UserInfoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function プロフィールページでユーザー情報が正しく取得できる()
    {
        // --- 1) ユーザー作成 ---
        $user = User::factory()->create([
            'user_name' => 'テスト太郎',
            'profile_image' => 'test_profile.jpg',
        ]);

        // --- 2) 出品商品を3個作成 ---
        $sell1 = Product::factory()->create([
            'user_id' => $user->id,
            'product_name' => '出品A',
        ]);
        $sell2 = Product::factory()->create([
            'user_id' => $user->id,
            'product_name' => '出品B',
        ]);

        // --- 3) 購入商品を2個作成 ---
        $buyProduct = Product::factory()->create([
            'product_name' => '購入商品X',
        ]);

        Purchase::create([
            'buyer_id'   => $user->id,
            'seller_id'  => $buyProduct->user_id,
            'product_id' => $buyProduct->id,
        ]);

        // --- 4) ログイン ---
        $this->actingAs($user);

        // --- 5) プロフィールページへアクセス ---
        $response = $this->get('/mypage');

        // --- 6) プロフィール情報の表示確認 ---
        $response->assertStatus(200);
        $response->assertSee('テスト太郎');                // ユーザー名
        $response->assertSee('出品した商品');              // 出品タブ
        $response->assertSee('購入した商品');              // 購入タブ

        // --- 7) 出品商品が表示されること ---
        $response->assertSee('出品A');
        $response->assertSee('出品B');

        // --- 8) 購入した商品が表示されること ---
        // 購入一覧を表示するため ?page=buy を付与
        $responseBuy = $this->get('/mypage?page=buy');
        $responseBuy->assertSee('購入商品X');
    }
}
