<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Orders\Application\Handlers;

use Cardoso\StartupKit\Core\Contracts\EventBus;
use Cardoso\StartupKit\Core\Primitives\Cqrs\CommandHandler;
use Cardoso\StartupKit\Core\Primitives\Result\Result;
use Cardoso\StartupKit\Core\ValueObjects\Money;
use Cardoso\StartupKit\Orders\Application\Commands\PlaceOrder;
use Cardoso\StartupKit\Orders\Contracts\OrderRepository;
use Cardoso\StartupKit\Orders\Domain\Events\OrderPlaced;
use Cardoso\StartupKit\Orders\Domain\Order;
use Cardoso\StartupKit\Orders\Domain\OrderId;
use Cardoso\StartupKit\Orders\Domain\OrderLine;

final class PlaceOrderHandler implements CommandHandler
{
    public function __construct(
        private readonly OrderRepository $repository,
        private readonly EventBus $eventBus,
    ) {}

    public function handle(\Cardoso\StartupKit\Core\Primitives\Cqrs\Command $command): Result
    {
        if (!$command instanceof PlaceOrder) {
            return Result::err(
                new \Cardoso\StartupKit\Core\Primitives\Errors\NotFoundError(
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
