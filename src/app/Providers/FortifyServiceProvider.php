<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redirect;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use App\Http\Responses\RegisterResponse as CustomRegisterResponse;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use App\Http\Requests\LoginRequest;


class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
            Fortify::registerView(function () {
                return view('auth.register');
            });

            Fortify::loginView(function () {
                return view('auth.login');
            });
            Fortify::authenticateUsing(function (Request $request) {
                $user = \App\Models\User::where('email', $request->email)->first();

                if (!$user) {
                    return null;
                }
                if (!\Hash::check($request->password, $user->password)) {
                    return null;
                }
                
                return $user;
            });

            $this->app->singleton(RegisterResponseContract::class, CustomRegisterResponse::class);

            RateLimiter::for('login', function (Request $request) {
                $email = (string) $request->email;

                return Limit::perMinute(10)->by($email . $request->ip());
            });

            $this->app->bind(FortifyLoginRequest::class, LoginRequest::class);
    }
}
