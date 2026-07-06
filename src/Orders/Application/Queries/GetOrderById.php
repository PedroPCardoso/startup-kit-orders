<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Orders\Application\Queries;

use Cardoso\StartupKit\Core\Primitives\Cqrs\Query;

final class GetOrderById implements Query
{
    public function __construct(
        public readonly string $orderId,
    ) {}
}
