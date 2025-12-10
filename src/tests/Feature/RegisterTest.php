<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 名前が未入力の場合はバリデーションエラーになる()
    {
        $response = $this->post('/register', [
            'user_name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'user_name' => 'お名前を入力してください',
        ]);
    }

    /** @test */
    public function メールアドレスが未入力の場合はバリデーションエラーになる()
    {
        $response = $this->post('/register', [
            'user_name' => 'テストユーザー',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    /** @test */
    public function パスワードが未入力の場合はバリデーションエラーになる()
    {
        $response = $this->post('/register', [
            'user_name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    /** @test */
    public function パスワードが8文字未満の場合はバリデーションエラーになる()
    {
        $response = $this->post('/register', [
            'user_name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください',
        ]);
    }

    /** @test */
    public function パスワード確認が一致しない場合はエラーになる()
    {
        $response = $this->post('/register', [
            'user_name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードと一致しません',
        ]);
    }

    /** @test */
    public function 全て正しく入力されている場合は登録成功しプロフィール設定へ遷移する()
    {
        $response = $this->post('/register', [
            'user_name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/mypage/profile');
    }
}