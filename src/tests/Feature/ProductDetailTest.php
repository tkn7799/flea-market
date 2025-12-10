<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\Comment;

class ProductDetailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 商品詳細情報が正しく表示される()
    {
        // ■ 出品者
        $user = User::factory()->create();

        // ■ 商品作成
        $product = Product::factory()->create([
            'user_id' => $user->id,
            'product_name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'description' => 'これは説明文です。',
            'condition' => '良好',
            'price' => 2000,
            'status' => 'selling',
        ]);

        // ■ カテゴリ 作成 & 紐付け
        $category = Category::factory()->create([
            'category_name' => '家電',
        ]);
        $product->categories()->attach($category->id);

        // ■ 商品画像
        $image = ProductImage::factory()->create([
            'product_id' => $product->id,
            'image_path' => 'test_image.jpg',
        ]);

        // ■ お気に入り数（いいね）
        Favorite::factory()->count(3)->create([
            'product_id' => $product->id,
        ]);

        // ■ コメント
        Comment::factory()->create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'comment' => 'テストコメントです',
        ]);

        // ■ 商品詳細ページへアクセス
        $response = $this->get(route('products.show', $product->id));

        // ▼ 表示確認
        $response->assertStatus(200);

        // 商品本体
        $response->assertSee('テスト商品');
        $response->assertSee('テストブランド');
        $response->assertSee('これは説明文です。');
        $response->assertSee('良好');
        $response->assertSee('¥2,000');

        // カテゴリ
        $response->assertSee('家電');

        // 画像パス
        $response->assertSee('test_image.jpg');

        // コメント
        $response->assertSee('テストコメントです');

        // いいね数（※ detail.blade で count() を表示している前提）
        $this->assertEquals(3, $product->favorites()->count());
        $response->assertSee((string)3);
    }
}