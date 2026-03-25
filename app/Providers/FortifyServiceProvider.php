<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;

use App\Http\Responses\LoginResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract ; 
use App\Http\Responses\RegisterResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract ; 

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void {
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);//fortify attend un contrat de reponse, on remplace l'implementation par defaut par la notre
        $this->app->singleton(RegisterResponseContract::class, RegisterResponse::class);//apres inscription fortify cree le user
    }

    public function boot(): void//la logique au demarage
    {

        //Fortify::ignoreRoutes();

        /*
          Désactivation des vues Blade
          (Fortify ne doit JAMAIS essayer de charger une vue dans une SPA)
         */
        Fortify::loginView(fn() => abort(404));
        Fortify::registerView(fn() => abort(404));
        Fortify::requestPasswordResetLinkView(fn () => abort(404));
        Fortify::resetPasswordView(fn () => abort(404));
        Fortify::verifyEmailView(fn () => abort(404));


        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        // Authentification API //ca remplace completement le login par deaut, on peut le supprimer
        Fortify::authenticateUsing(function ($request) {
            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                return $user;
            }

            return null;
        });

        // Rate limiting
        RateLimiter::for('login', function (Request $request) {
            $key = Str::lower($request->email).'|'.$request->ip();
            return Limit::perMinute(5)->by($key);
        });
    }
}
