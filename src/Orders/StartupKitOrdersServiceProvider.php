<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Orders;

use PedroPCardoso\StartupKit\Orders\Application\Handlers\GetOrderByIdHandler;
use PedroPCardoso\StartupKit\Orders\Application\Handlers\ListOrdersHandler;
use PedroPCardoso\StartupKit\Orders\Application\Handlers\PlaceOrderHandler;
use PedroPCardoso\StartupKit\Orders\Application\Queries\GetOrderById;
use PedroPCardoso\StartupKit\Orders\Application\Queries\ListOrders;
use PedroPCardoso\StartupKit\Orders\Application\Commands\PlaceOrder;
use PedroPCardoso\StartupKit\Orders\Contracts\OrderRepository;
use PedroPCardoso\StartupKit\Orders\Infrastructure\Persistence\MySql\MySqlOrderRepository;
use PedroPCardoso\StartupKit\Orders\Infrastructure\Persistence\Postgres\PostgresOrderRepository;
use Illuminate\Support\ServiceProvider;

final class StartupKitOrdersServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/startup-kit-orders.php', 'startup-kit-orders');

        $this->bindRepository();
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/startup-kit-orders.php' => config_path('startup-kit-orders.php'),
        ], 'startup-kit-orders-config');

        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');

        $this->registerHandlers();
    }

    private function bindRepository(): void
    {
        $driver = config('startup-kit-orders.default_driver', 'mysql');

        $this->app->bind(OrderRepository::class, match ($driver) {
            'pgsql', 'postgres' => PostgresOrderRepository::class,
            default             => MySqlOrderRepository::class,
        });
    }

    private function registerHandlers(): void
    {
        $commandBus = $this->app->make(\PedroPCardoso\StartupKit\Core\Primitives\Cqrs\CommandBus::class);
        $queryBus   = $this->app->make(\PedroPCardoso\StartupKit\Core\Primitives\Cqrs\QueryBus::class);

        $commandBus->register(PlaceOrder::class, PlaceOrderHandler::class);
        $queryBus->register(GetOrderById::class, GetOrderByIdHandler::class);
        $queryBus->register(ListOrders::class, ListOrdersHandler::class);
    }
}
