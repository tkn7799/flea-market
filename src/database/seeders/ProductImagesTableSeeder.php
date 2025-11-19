<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductImagesTableSeeder extends Seeder
{
    public function run()
    {
        $images = [
            ['product_id' => 1, 'image_path' => 'product_images/watch.jpg'],
            ['product_id' => 2, 'image_path' => 'product_images/HardDisk.jpg'],
            ['product_id' => 3, 'image_path' => 'product_images/onion.jpg'],
            ['product_id' => 4, 'image_path' => 'product_images/LeatherShoes.jpg'],
            ['product_id' => 5, 'image_path' => 'product_images/LivingRoomLaptop.jpg'],
            ['product_id' => 6, 'image_path' => 'product_images/MusicMic.jpg'],
            ['product_id' => 7, 'image_path' => 'product_images/fashionbag.jpg'],
            ['product_id' => 8, 'image_path' => 'product_images/Tumbler.jpg'],
            ['product_id' => 9, 'image_path' => 'product_images/Coffeemill.jpg'],
            ['product_id' => 10, 'image_path' => 'product_images/MakeupSet.jpg'],
        ];

        DB::table('product_images')->insert($images);
    }
}
