<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Address;
use App\Models\Purchase;

class AddressChangeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ① 変更した住所が購入画面に反映される
     */
    public function test_配送先変更で登録した住所が購入画面に反映される()
    {
        $user = User::factory()->create();

        $product = Product::factory()->create([
            'status' => 'selling',
        ]);

        $this->actingAs($user);

        // --- 登録する住所 ---
        $addressData = [
            'postal_code' => '123-4567',
            'address'     => '東京都新宿区テスト1丁目',
            'building'    => 'テストマンション101',
        ];

        // ① 送付先住所更新
        $this->post("/purchase/address/{$product->id}", $addressData);

        // DBに登録されていること
        $this->assertDatabaseHas('addresses', [
            'user_id'      => $user->id,
            'type'         => 'shipping',
            'postal_code'  => '123-4567',
        ]);

        // ② 購入画面へアクセス
        $response = $this->get("/purchase/{$product->id}");

        // ③ 購入画面へ正しく反映されていること
        $response->assertSee('123-4567');
        $response->assertSee('東京都新宿区テスト1丁目');
        $response->assertSee('テストマンション101');
    }

    /**
     * ② 購入した商品に送付先住所が紐づいて登録される
     */
    public function test_購入時に配送先住所が購入情報に紐づく()
    {
        $user = User::factory()->create();

        $product = Product::factory()->create([
            'status' => 'selling',
        ]);

        $this->actingAs($user);

        // --- 送付先住所を事前登録 ---
        $address = Address::create([
            'user_id'     => $user->id,
            'type'        => 'shipping',
            'postal_code' => '987-6543',
            'address'     => '大阪市中央区テスト2丁目',
            'building'    => 'テストタワー202',
        ]);

        // テスト環境では execute はダイレクト完了する仕様
        $this->post("/purchase/execute/{$product->id}");

        // 購入情報が保存されていること
        $this->assertDatabaseHas('purchases', [
            'buyer_id'   => $user->id,
            'product_id' => $product->id,
        ]);

        // 商品ステータスが sold に変更されていること
        $this->assertDatabaseHas('products', [
            'id'     => $product->id,
            'status' => 'sold',
        ]);

        // 紐づく住所が正しい（最新 shipping address を参照）
        $latestAddress = Address::where('user_id', $user->id)
                                ->where('type', 'shipping')
                                ->first();

        $this->assertEquals('987-6543', $latestAddress->postal_code);
    }
}
