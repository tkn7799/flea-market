<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'profile_image' => 'nullable|mimes:jpeg,png',
            'user_name'     => 'required|string|max:20',
            'postal_code'   => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address'       => 'required|string',
            'building'      => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'profile_image.mimes' => 'プロフィール画像はJPEGまたはPNGのみ使用できます',

            'user_name.required' => 'ユーザー名を入力してください',
            'user_name.max'      => 'ユーザー名は20文字以内で入力してください',

            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.regex'    => '郵便番号は「123-4567」の形式で入力してください',

            'address.required' => '住所を入力してください',
        ];
    }
}
