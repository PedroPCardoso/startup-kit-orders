<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Orders\Application\Handlers;

use Cardoso\StartupKit\Core\Primitives\Cqrs\Query;
use Cardoso\StartupKit\Core\Primitives\Cqrs\QueryHandler;
use Cardoso\StartupKit\Core\Primitives\Errors\ValidationError;
use Cardoso\StartupKit\Core\Primitives\Result\Result;
use Cardoso\StartupKit\Orders\Application\Queries\ListOrders;
use Cardoso\StartupKit\Orders\Contracts\OrderRepository;

final class ListOrdersHandler implements QueryHandler
{
    public function __construct(
        private readonly OrderRepository $repository,
    ) {}

    public function handle(Query $query): Result
    {
        if (!$query instanceof ListOrders) {
            return Result::err(ValidationError::make('query', 'Expected ListOrders.'));
        }

        return $this->repository->list(
            customerId: $query->customerId,
            status: $query->status,
        );
    }
}
