<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCategoryTableSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // 腕時計 (product_id = 1)
            ['product_id' => 1, 'category_id' => 1],  // ファッション
            ['product_id' => 1, 'category_id' => 5],  // メンズ
            ['product_id' => 1, 'category_id' => 12], // アクセサリー

            // HDD (product_id = 2)
            ['product_id' => 2, 'category_id' => 2],  // 家電

            // 玉ねぎ3束 (product_id = 3)
            ['product_id' => 3, 'category_id' => 10], // キッチン

            // 革靴 (product_id = 4)
            ['product_id' => 4, 'category_id' => 1],  // ファッション
            ['product_id' => 4, 'category_id' => 5],  // メンズ

            // ノートPC (product_id = 5)
            ['product_id' => 5, 'category_id' => 2],  // 家電

            // マイク (product_id = 6)
            ['product_id' => 6, 'category_id' => 2],  // 家電

            // ショルダーバッグ (product_id = 7)
            ['product_id' => 7, 'category_id' => 1],  // ファッション
            ['product_id' => 7, 'category_id' => 4],  // レディース

            // タンブラー (product_id = 8)
            ['product_id' => 8, 'category_id' => 10], // キッチン

            // コーヒーミル (product_id = 9)
            ['product_id' => 9, 'category_id' => 10], // キッチン

            // メイクセット (product_id = 10)
            ['product_id' => 10, 'category_id' => 1], // ファッション
            ['product_id' => 10, 'category_id' => 6], // コスメ
            ['product_id' => 10, 'category_id' => 4], // レディース
        ];

        DB::table('product_category')->insert($data);
    }
}
