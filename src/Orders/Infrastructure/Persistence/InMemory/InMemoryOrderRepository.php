<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Orders\Infrastructure\Persistence\InMemory;

use PedroPCardoso\StartupKit\Core\Primitives\Errors\NotFoundError;
use PedroPCardoso\StartupKit\Core\Primitives\Result\Result;
use PedroPCardoso\StartupKit\Orders\Contracts\OrderRepository;
use PedroPCardoso\StartupKit\Orders\Domain\Order;
use PedroPCardoso\StartupKit\Orders\Domain\OrderId;

final class InMemoryOrderRepository implements OrderRepository
{
    /** @var array<string, Order> */
    private array $store = [];

    public function save(Order $order): Result
    {
        $this->store[$order->id()->value()] = $order;

        return Result::ok(null);
    }

    public function byId(OrderId $id): Result
    {
        $order = $this->store[$id->value()] ?? null;

        if ($order === null) {
            return Result::err(NotFoundError::make('Order', $id->value()));
        }

        return Result::ok($order);
    }

    public function delete(Order $order): Result
    {
        unset($this->store[$order->id()->value()]);

        return Result::ok(null);
    }

    public function list(?string $customerId = null, ?string $status = null): Result
    {
        $orders = array_values($this->store);

        if ($customerId !== null) {
            $orders = array_filter($orders, fn(Order $o) => $o->customerId() === $customerId);
        }

        if ($status !== null) {
            $orders = array_filter($orders, fn(Order $o) => $o->status()->value === $status);
        }

        return Result::ok(array_values($orders));
    }
}
