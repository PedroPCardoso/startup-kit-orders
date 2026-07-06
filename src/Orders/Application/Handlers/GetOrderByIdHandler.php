<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Orders\Application\Handlers;

use Cardoso\StartupKit\Core\Primitives\Cqrs\Query;
use Cardoso\StartupKit\Core\Primitives\Cqrs\QueryHandler;
use Cardoso\StartupKit\Core\Primitives\Errors\ValidationError;
use Cardoso\StartupKit\Core\Primitives\Result\Result;
use Cardoso\StartupKit\Orders\Application\Queries\GetOrderById;
use Cardoso\StartupKit\Orders\Contracts\OrderRepository;
use Cardoso\StartupKit\Orders\Domain\OrderId;

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
