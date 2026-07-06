<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Orders\Domain\Events;

use PedroPCardoso\StartupKit\Orders\Domain\OrderId;
use PedroPCardoso\StartupKit\Orders\Domain\OrderStatus;

final class OrderPlaced
{
    public function __construct(
        public readonly OrderId $orderId,
        public readonly OrderStatus $status,
        public readonly array $lineData,
        public readonly ?string $customerId,
        public readonly \DateTimeImmutable $occurredAt = new \DateTimeImmutable(),
    ) {}
}
