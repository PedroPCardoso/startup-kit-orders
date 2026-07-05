<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Orders;

use Illuminate\Support\ServiceProvider;

final class StartupKitOrdersServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/startup-kit-orders.php', 'startup-kit-orders');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/startup-kit-orders.php' => config_path('startup-kit-orders.php'),
        ], 'startup-kit-orders-config');
    }
}
