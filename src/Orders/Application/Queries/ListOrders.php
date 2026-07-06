<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Orders\Application\Queries;

use PedroPCardoso\StartupKit\Core\Primitives\Cqrs\Query;

final class ListOrders implements Query
{
    public function __construct(
        public readonly ?string $customerId = null,
        public readonly ?string $status = null,
        public readonly int $perPage = 15,
        public readonly int $page = 1,
    ) {}
}
