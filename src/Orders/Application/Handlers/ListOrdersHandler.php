<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Orders\Application\Handlers;

use PedroPCardoso\StartupKit\Core\Primitives\Cqrs\Query;
use PedroPCardoso\StartupKit\Core\Primitives\Cqrs\QueryHandler;
use PedroPCardoso\StartupKit\Core\Primitives\Errors\ValidationError;
use PedroPCardoso\StartupKit\Core\Primitives\Result\Result;
use PedroPCardoso\StartupKit\Orders\Application\Queries\ListOrders;
use PedroPCardoso\StartupKit\Orders\Contracts\OrderRepository;

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
