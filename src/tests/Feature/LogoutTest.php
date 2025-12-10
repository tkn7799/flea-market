<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_logout()
    {
        // ① テストユーザー作成
        $user = User::factory()->create();

        // ② ユーザーとしてログイン
        $this->actingAs($user);

        // ③ POST /logout を実行
        $response = $this->post('/logout');

        // ④ ログイン画面 /login へリダイレクトされること
        $response->assertRedirect('/');

        // ⑤ 認証されていない状態になっていること
        $this->assertGuest();
    }
}
