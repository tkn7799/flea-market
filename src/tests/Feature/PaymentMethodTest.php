<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * 支払い方法選択画面が開ける
     */
    public function 支払い方法選択画面が開ける()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user);

        $response = $this->get("/purchase/{$product->id}");

        $response->assertStatus(200);

        // 画面にプルダウンが存在すること
        $response->assertSee('payment_method');
        $response->assertSee('カード払い');
        $response->assertSee('コンビニ払い');
    }

    /**
     * @test
     * プルダウンで選択した支払い方法が反映される
     */
    public function プルダウンで選択した支払い方法が反映される()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user);

        // 支払い方法「コンビニ払い」を選択して送信
        $response = $this->post("/purchase/execute/{$product->id}", [
            'payment_method' => 'convenience',
        ]);

        // Stripe にリダイレクトせず、反映テストのため成功画面へ飛ぶように調整している想定
        // 「支払い方法が反映されました」という画面内表示をチェック
        $response->assertSessionHas('payment_method', 'convenience');
    }
}
