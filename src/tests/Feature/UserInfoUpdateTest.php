<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Address;

class UserInfoUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function プロフィール編集画面で過去設定したユーザー情報が初期値として表示される()
    {
        // --- ①ユーザー作成 ---
        $user = User::factory()->create([
            'user_name'     => '山田 太郎',
            'email'         => 'taro@example.com',
            'profile_image' => 'test_profile.jpg',
        ]);

        // --- ②住所レコード作成（registered と shipping の両方を用意） ---
        Address::create([
            'user_id'     => $user->id,
            'type'        => 'registered',
            'postal_code' => '123-4567',
            'address'     => '東京都品川区1-1-1',
            'building'    => '品川ビル301',
        ]);

        Address::create([
            'user_id'     => $user->id,
            'type'        => 'shipping',
            'postal_code' => '987-6543',
            'address'     => '大阪府大阪市2-2-2',
            'building'    => '大阪マンション202',
        ]);

        // --- ③ログイン ---
        $this->actingAs($user);

        // --- ④プロフィール編集画面を開く ---
        $response = $this->get('/mypage/profile');

        // --- ⑤ 初期値が正しく表示されていることを確認 ---
        $response->assertStatus(200);

        // ユーザー名
        $response->assertSee('山田 太郎');

        // プロフィール画像（ファイル名が含まれているか）
        $response->assertSee('test_profile.jpg');

        // registered address が表示されていること
        $response->assertSee('123-4567');
        $response->assertSee('東京都品川区1-1-1');
        $response->assertSee('品川ビル301');

    }
}
