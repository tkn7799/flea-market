<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;

class ProductSearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * 「商品名」で部分一致検索ができる
     */
    public function 商品名で部分一致検索ができる()
    {
        // 商品を3つ作成
        Product::factory()->create(['product_name' => '赤い帽子']);
        Product::factory()->create(['product_name' => '青い靴']);
        Product::factory()->create(['product_name' => '赤いシャツ']);

        // 「赤」で検索
        $response = $this->get('/?keyword=赤');

        // 部分一致するものが表示される
        $response->assertSee('赤い帽子');
        $response->assertSee('赤いシャツ');

        // 一致しないものは表示されない
        $response->assertDontSee('青い靴');
    }

    /**
     * @test
     * 検索状態がマイリストでも保持されている
     */
    public function 検索状態がマイリストでも保持されている()
    {
        $response = $this->get('/?tab=mylist&keyword=テスト');
        $response->assertSee('value="テスト"', false);
    }
}
