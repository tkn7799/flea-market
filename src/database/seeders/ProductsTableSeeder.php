<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductImage;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            [
                'user_id' => 1,
                'product_name' => '腕時計',
                'price' => 15000,
                'brand_name' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'condition' => '良好',
                'img_url' => 'watch.jpg',
                ],
            [
                'user_id' => 2,
                'product_name' => 'HDD',
                'price' => 5000,
                'brand_name' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'condition' => '目立った傷や汚れなし',
                'img_url' => 'HardDisk.jpg',
                ],
            [
                'user_id' => 3,
                'product_name' => '玉ねぎ3束',
                'price' => 300,
                'brand_name' => 'なし',
                'description' => '新鮮な玉ねぎ3束のセット',
                'condition' => 'やや傷や汚れあり',
                'img_url' => 'onion.jpg',
            ],
            [
                'user_id' => 1,
                'product_name' => '革靴',
                'price' => 4000,
                'brand_name' => null,
                'description' => 'クラシックなデザインの革靴',
                'condition' => '状態が悪い',
                'img_url' => 'LeatherShoes.jpg',
            ],
            [
                'user_id' => 2,
                'product_name' => 'ノートPC',
                'price' => 45000,
                'brand_name' => null,
                'description' => '高性能なノートパソコン',
                'condition' => '良好',
                'img_url' => 'LivingRoomLaptop.jpg',
            ],
            [
                'user_id' => 3,
                'product_name' => 'マイク',
                'price' => 8000,
                'brand_name' => 'なし',
                'description' => '高音質のレコーディング用マイク',
                'condition' => '目立った傷や汚れなし',
                'img_url' => 'MusicMic.jpg',
            ],
            [
                'user_id' => 2,
                'product_name' => 'ショルダーバッグ',
                'price' => 3500,
                'brand_name' => null,
                'description' => 'おしゃれなショルダーバッグ',
                'condition' => 'やや傷や汚れあり',
                'img_url' => 'fashionbag.jpg',
            ],
            [
                'user_id' => 1,
                'product_name' => 'タンブラー',
                'price' => 500,
                'brand_name' => 'なし',
                'description' => '使いやすいタンブラー',
                'condition' => '状態が悪い',
                'img_url' => 'Tumbler.jpg',
            ],
            [
                'user_id' => 3,
                'product_name' => 'コーヒーミル',
                'price' => 4000,
                'brand_name' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'condition' => '良好',
                'img_url' => 'Coffeemill.jpg',
            ],
            [
                'user_id' => 2,
                'product_name' => 'メイクセット',
                'price' => 2500,
                'brand_name' => null,
                'description' => '便利なメイクアップセット',
                'condition' => '目立った傷や汚れなし',
                'img_url' => 'MakeupSet.jpg',
            ],
        ];

        foreach ($items as $item) {
            $product = Product::create([
                'user_id' => $item['user_id'],
                'product_name' => $item['product_name'],
                'price' => $item['price'],
                'brand_name' => $item['brand_name'],
                'description' => $item['description'],
                'condition' => $item['condition'],
                'status'       => 'selling',
            ]);

            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => 'product_images/' . $item['img_url'],
            ]);
        }
    }
}
