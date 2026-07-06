<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Orders\Application\Queries;

use Cardoso\StartupKit\Core\Primitives\Cqrs\Query;

final class ListOrders implements Query
{
    public function __construct(
        public readonly ?string $customerId = null,
        public readonly ?string $status = null,
        public readonly int $perPage = 15,
        public readonly int $page = 1,
    ) {}
}
