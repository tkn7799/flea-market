<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_name' => 'required|string|max:20',
            'email'     => 'required|email',
            'password'  => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8'
        ];
    }

    public function messages()
    {
        return [
            'user_name.required' => 'ユーザー名を入力してください',
            'user_name.max'      => 'ユーザー名は20文字以内で入力してください',

            'email.required' => 'メールアドレスを入力してください',
            'email.email'    => '正しいメールアドレスを入力してください',

            'password.required' => 'パスワードを入力してください',
            'password.min'      => 'パスワードは8文字以上で入力してください',

            'password_confirmation.required' => '確認用パスワードを入力してください',
            'password.confirmed'             => 'パスワードが一致しません',
        ];
    }
}
