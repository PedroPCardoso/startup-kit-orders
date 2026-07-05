<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Orders\Application\Commands;

use Cardoso\StartupKit\Core\Primitives\Cqrs\Command;

final class PlaceOrder implements Command
{
    /**
     * @param list<array{product_id: string, description: string, quantity: int, unit_price: int}> $items
     */
    public function __construct(
        public readonly array $items,
        public readonly ?string $customerId = null,
    ) {}
}
