<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Gate;
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
            $greeting = "Xin chào " . $notifiable->name;
            return (new MailMessage)
                ->subject('Xác minh địa chỉ email')
                ->greeting($greeting )
                ->line('Click vào nút bên dưới để xác mimh địa chỉ email của bạn')
                ->action('Xác minh địa chỉ email', $url);
        });
    }
}
