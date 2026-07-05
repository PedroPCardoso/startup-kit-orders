<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Orders\Domain\Events;

use Cardoso\StartupKit\Orders\Domain\OrderId;
use Cardoso\StartupKit\Orders\Domain\OrderStatus;

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
