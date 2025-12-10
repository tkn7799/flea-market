<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        // 未認証（メール未認証）の場合は verify 画面へ
        if (!$request->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        // 認証済みならマイページへ
        return redirect('/mypage');
    }
}
