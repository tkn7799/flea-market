<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Comment;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ① ログイン済みユーザーはコメントを送信できる
     */
    public function test_ログイン済みユーザーはコメントを送信できる()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user);

        $response = $this->post("/comment/{$product->id}", [
            'comment' => 'テストコメントです',
        ]);

        // 成功後は詳細ページへリダイレクト
        $response->assertRedirect("/item/{$product->id}");

        // コメントが保存される
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'comment' => 'テストコメントです',
        ]);

        // コメント数が1増えている
        $this->assertEquals(1, Comment::where('product_id', $product->id)->count());
    }

    /**
     * ② 未ログインユーザーはコメントを送信できない
     */
    public function test_未ログインユーザーはコメントを送信できない()
    {
        $product = Product::factory()->create();

        $response = $this->post("/comment/{$product->id}", [
            'comment' => 'ログインしていません',
        ]);

        // ログイン画面へリダイレクト（302）
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        // 保存されていない
        $this->assertDatabaseCount('comments', 0);
    }

    /**
     * ③ コメント未入力のときバリデーションエラーになる
     */
    public function test_コメント未入力の場合バリデーションメッセージが表示される()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user);

        $response = $this->from("/item/{$product->id}")
                        ->post("/comment/{$product->id}", [
                            'comment' => '',
                        ]);

        // バリデーションで元画面に戻る
        $response->assertStatus(302);
        $response->assertRedirect("/item/{$product->id}");

        // エラーメッセージを確認
        $response->assertSessionHasErrors(['comment']);

        // 保存されていない
        $this->assertDatabaseCount('comments', 0);
    }

    /**
     * ④ コメントが255文字超過の場合バリデーションエラー
     */
    public function test_コメントが255文字超過の場合バリデーションメッセージが表示される()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user);

        $longText = str_repeat('あ', 256);

        $response = $this->from("/item/{$product->id}")
                        ->post("/comment/{$product->id}", [
                            'comment' => $longText,
                        ]);

        $response->assertStatus(302);
        $response->assertRedirect("/item/{$product->id}");
        $response->assertSessionHasErrors(['comment']);

        // 保存されていない
        $this->assertDatabaseCount('comments', 0);
    }
}
