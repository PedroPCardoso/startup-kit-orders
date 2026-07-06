<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Orders\Domain;

use PedroPCardoso\StartupKit\Core\ValueObjects\Money;

final class OrderLine
{
    public function __construct(
        private readonly string $productId,
        private readonly string $description,
        private readonly int $quantity,
        private readonly Money $unitPrice,
    ) {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive.');
        }
    }

    public function productId(): string
    {
        return $this->productId;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    public function unitPrice(): Money
    {
        return $this->unitPrice;
    }

    public function total(): Money
    {
        return $this->unitPrice->mul($this->quantity);
    }
}
