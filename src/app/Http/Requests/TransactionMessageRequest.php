<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
public function rules()
    {
        return [
            'message' => [
                'required',
                'string',
                'max:400', // 本文400文字以内
            ],
            'image' => [
                'nullable',
                'mimes:png,jpeg', // 画像形式の制限
                'max:2048',           // 2MB以内
            ],
        ];
    }

    /**
     * エラーメッセージのカスタマイズ
     */
    public function messages()
    {
        return [
            'message.required' => '本文を入力してください',
            'message.max' => '本文は400文字以内で入力してください',
            'image.mimes' => '「.png」または「.jpeg」形式でアップロードしてください',
            'image.max' => '画像サイズは2MB以内でアップロードしてください',
        ];
    }
}
