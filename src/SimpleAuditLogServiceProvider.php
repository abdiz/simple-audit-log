<?php


namespace AbdiZbn\SimpleAuditLog;
use Illuminate\Support\ServiceProvider;

class SimpleAuditLogServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/migrations');

        if ($this->app->runningInConsole()) {

            $this->publishes([
                __DIR__.'/migrations' => database_path('migrations'),
            ], 'migrations');

            $this->publishes([
                __DIR__.'/../config/audit.php' => config_path('audit.php'),
            ], 'config');

        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/audit.php', 'audit');
    }
}
