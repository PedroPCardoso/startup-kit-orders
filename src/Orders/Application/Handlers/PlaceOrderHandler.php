<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Orders\Application\Handlers;

use PedroPCardoso\StartupKit\Core\Contracts\EventBus;
use PedroPCardoso\StartupKit\Core\Primitives\Cqrs\CommandHandler;
use PedroPCardoso\StartupKit\Core\Primitives\Result\Result;
use PedroPCardoso\StartupKit\Core\ValueObjects\Money;
use PedroPCardoso\StartupKit\Orders\Application\Commands\PlaceOrder;
use PedroPCardoso\StartupKit\Orders\Contracts\OrderRepository;
use PedroPCardoso\StartupKit\Orders\Domain\Events\OrderPlaced;
use PedroPCardoso\StartupKit\Orders\Domain\Order;
use PedroPCardoso\StartupKit\Orders\Domain\OrderId;
use PedroPCardoso\StartupKit\Orders\Domain\OrderLine;

final class PlaceOrderHandler implements CommandHandler
{
    public function __construct(
        private readonly OrderRepository $repository,
        private readonly EventBus $eventBus,
    ) {}

    public function handle(\PedroPCardoso\StartupKit\Core\Primitives\Cqrs\Command $command): Result
    {
        if (!$command instanceof PlaceOrder) {
            return Result::err(
                new \PedroPCardoso\StartupKit\Core\Primitives\Errors\NotFoundError(
                    'handler.mismatch',
                    'Invalid command type.',
                )
            );
        }

        $lines = [];
        foreach ($command->items as $item) {
            $lines[] = new OrderLine(
                productId: $item['product_id'],
                description: $item['description'],
                quantity: $item['quantity'],
                unitPrice: Money::fromInt($item['unit_price'], 'BRL'),
            );
        }

        $orderId = OrderId::generate();

        $result = Order::place($orderId, $lines, $command->customerId);

        if ($result->isErr()) {
            return $result;
        }

        $order = $result->unwrap();

        $persistResult = $this->repository->save($order);

        if ($persistResult->isErr()) {
            return $persistResult;
        }

        $this->eventBus->publish(new OrderPlaced(
            orderId: $orderId,
            status: $order->status(),
            lineData: $command->items,
            customerId: $command->customerId,
        ));

        return Result::ok($orderId);
    }
}
