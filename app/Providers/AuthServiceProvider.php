<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Lang;
use Laravel\Passport\Passport;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
        Passport::routes();
        Passport::tokensExpireIn(now()->addSecond(60));
        // Passport::tokensExpireIn(now()->addDays(1));
        // Passport::refreshTokensExpireIn(now()->addDays(1));
        // Passport::personalAccessTokensExpireIn(now()->addMonths(6));
        //Ghi đè phương thức xác thực email
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {

            //dd($notifiable);
            $greeting = "Xin chào: " . $notifiable->name;
            return (new MailMessage)
                ->subject('Xác minh địa chỉ email')
                ->greeting($greeting )
                ->line('Click vào nút bên dưới để xác mimh địa chỉ email của bạn')
                ->action('Xác minh địa chỉ email', $url);
        });

        ResetPassword::toMailUsing(function (object $notifiable, string $token) {

            $greeting = "Xin chào: " . $notifiable->name;
            return (new MailMessage)
            ->greeting($greeting )
            ->subject(Lang::get('Reset Password Notification'))
            ->line(Lang::get('You are receiving this email because we received a password reset request for your account.'))
            ->action(Lang::get('Reset Password'), route('password.reset', $token))
            ->line(Lang::get('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
            ->line(Lang::get('If you did not request a password reset, no further action is required.'));
        });

     
    }
}
