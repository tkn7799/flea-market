<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Favorite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ① いいねを追加できる
     */
    public function test_いいねを追加できる()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user);

        // JSON で送ることで FavoriteController の wantsJson() を通す
        $response = $this->postJson("/favorite/{$product->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'added',
                    'count'  => 1,
                ]);

        $this->assertDatabaseHas('favorites', [
            'user_id'    => $user->id,
            'product_id' => $product->id,
        ]);
    }

    /**
     * ② いいねを解除できる
     */
    public function test_いいねを解除できる()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        // 事前にいいね状態を作る
        Favorite::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $this->actingAs($user);

        // JSON で送信
        $response = $this->postJson("/favorite/{$product->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'removed',
                    'count'  => 0,
                ]);

        $this->assertDatabaseMissing('favorites', [
            'user_id'    => $user->id,
            'product_id' => $product->id,
        ]);
    }

    /**
     * ③ 未ログイン状態では 401 + redirect JSON が返る
     */
    public function test_未ログイン()
    {
        $product = Product::factory()->create();

        $response = $this->postJson("/favorite/{$product->id}");

        $response->assertStatus(401)
                ->assertJson([
                    'status' => 'unauthenticated',
                    'redirect' => route('login'),
                ]);
    }
}