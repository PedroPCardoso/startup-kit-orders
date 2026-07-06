<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Orders\Http\Controllers;

use PedroPCardoso\StartupKit\Core\Api\Http\BaseController;
use PedroPCardoso\StartupKit\Core\Primitives\Cqrs\CommandBus;
use PedroPCardoso\StartupKit\Core\Primitives\Cqrs\QueryBus;
use PedroPCardoso\StartupKit\Orders\Application\Commands\PlaceOrder;
use PedroPCardoso\StartupKit\Orders\Application\Queries\GetOrderById;
use PedroPCardoso\StartupKit\Orders\Application\Queries\ListOrders;
use PedroPCardoso\StartupKit\Orders\Domain\Order;
use PedroPCardoso\StartupKit\Orders\Http\Requests\PlaceOrderRequest;
use PedroPCardoso\StartupKit\Orders\Http\Resources\OrderResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class OrderController extends BaseController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly QueryBus $queryBus,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $result = $this->queryBus->ask(new ListOrders(
            customerId: $request->query('customer_id'),
            status: $request->query('status'),
        ));

        return $this->respondWith(
            $result->map(fn(array $orders) => array_map(
                fn(Order $order) => new OrderResource($order),
                $orders,
            )),
        );
    }

    public function show(string $id): JsonResponse
    {
        $result = $this->queryBus->ask(new GetOrderById($id));

        return $this->respondWith(
            $result->map(fn(Order $order) => new OrderResource($order)),
        );
    }

    public function store(PlaceOrderRequest $request): JsonResponse
    {
        $result = $this->commandBus->dispatch(new PlaceOrder(
            items: $request->validated('items'),
            customerId: $request->validated('customer_id'),
        ));

        return $this->respondWith($result, 201);
    }
}
