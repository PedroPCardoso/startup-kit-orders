<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Orders\Contracts;

use PedroPCardoso\StartupKit\Core\Primitives\Result\Result;
use PedroPCardoso\StartupKit\Orders\Domain\Order;
use PedroPCardoso\StartupKit\Orders\Domain\OrderId;

interface OrderRepository
{
    public function save(Order $order): Result;

    public function byId(OrderId $id): Result;

    public function delete(Order $order): Result;

    /**
     * @return Result<list<Order>>
     */
    public function list(?string $customerId = null, ?string $status = null): Result;
}
