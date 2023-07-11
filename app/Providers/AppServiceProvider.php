<?php

namespace App\Providers;

use App\Validators\ReCaptcha;
use DateTime;
use Illuminate\Database\Eloquent\Model;
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
        $this->logDatabaseQueries();
        $this->detectLazyLoadingViolation();

        Validator::extend('recaptcha',  [ReCaptcha::class, 'validate']);
        // Validator::extend('recaptcha', 'App\Validators\Recaptcha@validate');
    }

    private function loadMigrationsRecursive()
    {
        // Lấy recursive tất cả các thư mục con trong thư mục migrations
        $mainPath = database_path('migrations');
        $directories = glob($mainPath . '/*', GLOB_ONLYDIR);
        $paths = array_merge([$mainPath], $directories);

        $this->loadMigrationsFrom($paths);
    }

    private function detectLazyLoadingViolation()
    {
        Model::preventLazyLoading(!config('app.prevent_lazy_loading', false));

        Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
            $message = sprintf("N+1 Query detected in %s::%s", get_class($model), $relation);
            Log::channel('n+1')->debug($message);
        });
    }

    private function logDatabaseQueries()
    {
        DB::listen(function ($query) {
            try {
                if (config('app.log_database_queries') || config('app.log_slow_queries')) {
                    $sql = $query->sql;
                    $bindings = $query->bindings;

                    $sql = str_replace('?', '%s', $sql);
                    $bindings = array_map(function ($value) {
                        if ($value instanceof DateTime) {
                            return $value->format('Y-m-d H:i:s');
                        }

                        return $value;
                    }, $bindings);
                    $sql = vsprintf($sql, $bindings);

                    $message = sprintf('Connection: %s | Time: %s | SQL: %s', $query->connectionName, $query->time, $sql);

                    if (config('app.log_database_queries')) {
                        Log::channel('database-query')->debug($message);
                    }
                    if (config('app.log_slow_queries')) {
                        if ($query->time > config('app.slow_query_time')) {
                            Log::channel('database-slow-query')->debug($message);
                        }
                    }
                }
            } catch (\Exception $e) {
                dd($e->getMessage());
                //Log::channel('database')->error($e->getMessage());
            }
        });
    }
}
