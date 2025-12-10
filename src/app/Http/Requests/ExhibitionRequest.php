<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'images'      => 'required',
            'images.*'    => 'mimes:jpeg,png',
            'product_name' => 'required|string',
            'description'  => 'required|string|max:255',
            'categories'   => 'required|array|min:1',
            'condition'    => 'required|string',
            'price'        => 'required|numeric|min:0'
        ];
    }

    public function messages()
    {
        return [
            'images.required' => '商品画像を選択してください',
            'images.*.mimes'  => '商品画像はJPEGまたはPNGのみ使用できます',

            'product_name.required' => '商品名を入力してください',

            'description.required' => '商品説明を入力してください',
            'description.max'      => '商品説明は255文字以内で入力してください',

            'categories.required' => 'カテゴリーを選択してください',

            'condition.required' => '商品の状態を選択してください',

            'price.required' => '販売価格を入力してください',
            'price.numeric'  => '販売価格は数値で入力してください',
            'price.min'      => '販売価格は0円以上で入力してください',
        ];
    }
}
