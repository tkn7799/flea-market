<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;

class ProductCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_商品情報が正しく保存できる()
    {
        Storage::fake('public');

        // カテゴリ作成
        $cat1 = Category::create(['category_name' => '家電']);
        $cat2 = Category::create(['category_name' => '家具']);

        // ユーザー作成
        $user = User::factory()->create();
        $this->actingAs($user);

        // GD 不要のダミー画像ファイル
        $dummyImage = UploadedFile::fake()->create('test.jpg', 10, 'image/jpeg');

        // 商品登録
        $response = $this->post('/sell', [
            'product_name' => 'テスト商品',
            'brand_name'   => 'テストブランド',
            'condition'    => '良好',
            'description'  => 'これはテスト商品の説明です。',
            'price'        => 3000,
            'categories'   => [$cat1->id, $cat2->id],
            'images'       => [$dummyImage],
        ]);

        // 商品データ取得
        $product = Product::first();

        // 商品詳細へリダイレクト
        $response->assertRedirect("/item/{$product->id}");

        // 商品テーブル確認
        $this->assertDatabaseHas('products', [
            'product_name' => 'テスト商品',
            'user_id'      => $user->id,
        ]);

        // 中間テーブル確認
        $this->assertDatabaseHas('product_category', [
            'product_id'  => $product->id,
            'category_id' => $cat1->id,
        ]);

        // 画像保存確認
        Storage::disk('public')->assertExists($product->images->first()->image_path);
    }
}