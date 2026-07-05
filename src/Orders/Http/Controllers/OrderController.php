<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Orders\Http\Controllers;

use Cardoso\StartupKit\Core\Api\Http\BaseController;
use Cardoso\StartupKit\Core\Primitives\Cqrs\CommandBus;
use Cardoso\StartupKit\Orders\Application\Commands\PlaceOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class OrderController extends BaseController
{
    public function __construct(
        private readonly CommandBus $commandBus,
    ) {}

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|string',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|integer|min:0',
            'customer_id' => 'nullable|string',
        ]);

        $result = $this->commandBus->dispatch(new PlaceOrder(
            items: $validated['items'],
            customerId: $validated['customer_id'] ?? null,
        ));

        return $this->respondWith($result, 201);
    }
}
