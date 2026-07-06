<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Orders\Application\Queries;

use PedroPCardoso\StartupKit\Core\Primitives\Cqrs\Query;

final class GetOrderById implements Query
{
    public function __construct(
        public readonly string $orderId,
    ) {}
}
