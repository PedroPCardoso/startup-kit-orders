<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Orders\Application\Handlers;

use PedroPCardoso\StartupKit\Core\Primitives\Cqrs\Query;
use PedroPCardoso\StartupKit\Core\Primitives\Cqrs\QueryHandler;
use PedroPCardoso\StartupKit\Core\Primitives\Errors\ValidationError;
use PedroPCardoso\StartupKit\Core\Primitives\Result\Result;
use PedroPCardoso\StartupKit\Orders\Application\Queries\GetOrderById;
use PedroPCardoso\StartupKit\Orders\Contracts\OrderRepository;
use PedroPCardoso\StartupKit\Orders\Domain\OrderId;

final class GetOrderByIdHandler implements QueryHandler
{
    public function __construct(
        private readonly OrderRepository $repository,
    ) {}

    public function handle(Query $query): Result
    {
        if (!$query instanceof GetOrderById) {
            return Result::err(ValidationError::make('query', 'Expected GetOrderById.'));
        }

        try {
            $orderId = OrderId::fromString($query->orderId);
        } catch (\InvalidArgumentException) {
            return Result::err(ValidationError::make('order_id', 'Invalid UUID format.'));
        }

        return $this->repository->byId($orderId);
    }
}
