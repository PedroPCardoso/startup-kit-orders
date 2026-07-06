<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Orders\Contracts;

use Cardoso\StartupKit\Core\Primitives\Result\Result;
use Cardoso\StartupKit\Orders\Domain\Order;
use Cardoso\StartupKit\Orders\Domain\OrderId;

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
