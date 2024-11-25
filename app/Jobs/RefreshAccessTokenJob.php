<?php

namespace App\Jobs;

use App\Http\Controllers\Auth\JstController;
use App\JsOauthToken;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefreshAccessTokenJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
         // Lấy token từ DB
         $token = JsOauthToken::where('expired_time', '>', now()->addDay())->first();

         // Nếu token gần hết hạn
         $refreshToken = '54c4670655f3a85ad54637b387ad196f';
         if ($token && $token->expired_time < now()->addDay()) {
             $refreshToken = $token->refresh_token;

             // Gọi hàm refresh token từ OAuthController
             $controller = new JstController();
             $controller->refreshToken($refreshToken);
         }
    }
}
