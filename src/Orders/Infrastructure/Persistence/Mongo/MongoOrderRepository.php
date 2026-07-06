<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Orders\Infrastructure\Persistence\Mongo;

use Cardoso\StartupKit\Core\Primitives\Errors\NotFoundError;
use Cardoso\StartupKit\Core\Primitives\Result\Result;
use Cardoso\StartupKit\Orders\Contracts\OrderRepository;
use Cardoso\StartupKit\Orders\Domain\Order;
use Cardoso\StartupKit\Orders\Domain\OrderId;
use Cardoso\StartupKit\Orders\Infrastructure\StorageError;

/**
 * MongoDB implementation of OrderRepository.
 *
 * Requires: mongodb/laravel-mongodb (composer require mongodb/laravel-mongodb)
 * Add 'mongodb' connection to config/database.php before using.
 *
 * @todo Implement using mongodb/laravel-mongodb Eloquent model
 */
final class MongoOrderRepository implements OrderRepository
{
    public function save(Order $order): Result
    {
        return Result::err(StorageError::persistFailed('MongoDB driver not implemented yet.'));
    }

    public function byId(OrderId $id): Result
    {
        return Result::err(NotFoundError::make('Order', $id->value(), 'MongoDB driver not implemented yet.'));
    }

    public function delete(Order $order): Result
    {
        return Result::err(StorageError::deleteFailed('MongoDB driver not implemented yet.'));
    }

    public function list(?string $customerId = null, ?string $status = null): Result
    {
        return Result::err(StorageError::listFailed('MongoDB driver not implemented yet.'));
    }
}
