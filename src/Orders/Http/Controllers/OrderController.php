<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Orders\Http\Controllers;

use Cardoso\StartupKit\Core\Api\Http\BaseController;
use Cardoso\StartupKit\Core\Primitives\Cqrs\CommandBus;
use Cardoso\StartupKit\Core\Primitives\Cqrs\QueryBus;
use Cardoso\StartupKit\Orders\Application\Commands\PlaceOrder;
use Cardoso\StartupKit\Orders\Application\Queries\GetOrderById;
use Cardoso\StartupKit\Orders\Application\Queries\ListOrders;
use Cardoso\StartupKit\Orders\Domain\Order;
use Cardoso\StartupKit\Orders\Http\Requests\PlaceOrderRequest;
use Cardoso\StartupKit\Orders\Http\Resources\OrderResource;
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
