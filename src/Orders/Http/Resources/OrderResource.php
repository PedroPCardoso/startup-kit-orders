<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Orders\Http\Resources;

use PedroPCardoso\StartupKit\Orders\Domain\Order;
use PedroPCardoso\StartupKit\Orders\Domain\OrderLine;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Order $resource
 */
final class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        $order = $this->resource;

        return [
            'id'          => $order->id()->value(),
            'status'      => $order->status()->value,
            'customer_id' => $order->customerId(),
            'lines'       => array_map(
                fn(OrderLine $line) => [
                    'product_id'  => $line->productId(),
                    'description' => $line->description(),
                    'quantity'    => $line->quantity(),
                    'unit_price'  => $line->unitPrice()->amount(),
                    'currency'    => $line->unitPrice()->currency(),
                    'total'       => $line->total()->amount(),
                ],
                $order->lines(),
            ),
            'total'      => $order->total()->amount(),
            'currency'   => $order->lines() !== [] ? $order->lines()[0]->unitPrice()->currency() : null,
            'created_at' => $order->createdAt()->format(\DateTimeInterface::ATOM),
            'updated_at' => $order->updatedAt()?->format(\DateTimeInterface::ATOM),
        ];
    }
}
