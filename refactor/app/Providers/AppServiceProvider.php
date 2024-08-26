<?php
namespace DTApi\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(NotificationServiceInterface::class, NotificationService::class,
            EmailNotification::class);
        $this->app->singleton(SMSNotification::class, function ($app) {
            return new SMSNotification();
        });
        $this->app->singleton(PushNotification::class, function ($app) {
            return new PushNotification();
        });
        $this->app->singleton(EmailNotification::class, function ($app) {
            return new EmailNotification($app['mailer']);
        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Response::macro('api', function ($data, $status = 200, $headers = [], $options = 0) {
            return response()->json($data, $status, $headers, $options);
        });

    }
}
