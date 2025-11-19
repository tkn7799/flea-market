<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'id'        => 1,
                'user_name' => '出品者A',
                'email' => 'sellerA@example.com',
                'password' => Hash::make('password123'),
                'profile_image' => 'profile_images/user1.png',
            ],
            [
                'id'        => 2,
                'user_name' => '出品者B',
                'email' => 'sellerB@example.com',
                'password' => Hash::make('password123'),
                'profile_image' => 'profile_images/user2.png',
            ],
            [
                'id'        => 3,
                'user_name' => '出品者C',
                'email' => 'sellerC@example.com',
                'password' => Hash::make('password123'),
                'profile_image' => 'profile_images/user3.png',
            ],
            [
                'id'        => 4,
                'user_name' => 'テストユーザー',
                'email' => 'test@example.com',
                'password' => Hash::make('password123'),
                'profile_image' => 'profile_images/testuser.png',
            ],
        ];

        foreach ($users as $index => $user) {
            $userId = DB::table('users')->insertGetId([
                'user_name' => $user['user_name'],
                'email' => $user['email'],
                'password' => $user['password'],
                'profile_image' => $user['profile_image'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // ――― 住所情報（登録住所 = type = registered）―――
            DB::table('addresses')->insert([
                'user_id' => $userId,
                'type' => 'registered',
                'postal_code' => sprintf("100-%04d", rand(1000, 9999)),
                'address' => "東京都〇〇区〇〇町 " . ($index + 1) . "番地",
                'building' => "テストビル " . ($index + 100),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

