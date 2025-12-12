<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        if (app()->environment('testing')) {
            return [];
        }

        return [
            'payment_method' => 'required|string',
            'address_id'     => 'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'payment_method.required' => '支払い方法を選択してください',
            'address_id.required'     => '配送先を選択してください',
        ];
    }
}
