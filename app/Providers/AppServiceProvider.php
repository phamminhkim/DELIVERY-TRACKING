<?php

namespace App\Providers;

use App\Validators\ReCaptcha;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use PgSql\Lob;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsRecursive();
        if (config('app.log_database_queries')) {
            $this->logDatabaseQueries();
        }

        Validator::extend('recaptcha',  [ReCaptcha::class,'validate']);
       // Validator::extend('recaptcha', 'App\Validators\Recaptcha@validate');
    }

    public function loadMigrationsRecursive()
    {
        // Lấy recursive tất cả các thư mục con trong thư mục migrations
        $mainPath = database_path('migrations');
        $directories = glob($mainPath . '/*', GLOB_ONLYDIR);
        $paths = array_merge([$mainPath], $directories);

        $this->loadMigrationsFrom($paths);
    }

    public function logDatabaseQueries()
    {
        DB::listen(function ($query) {
            $sql = $query->sql;
            $bindings = $query->bindings;

            $sql = str_replace('?', '%s', $sql);
            $sql = vsprintf($sql, $bindings);

            $message = sprintf('Connection: %s | Time: %s | SQL: %s', $query->connectionName, $query->time, $sql);

            Log::channel('database')->debug($message);
        });
    }
}
