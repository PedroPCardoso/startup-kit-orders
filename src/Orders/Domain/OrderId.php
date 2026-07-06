<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Orders\Domain;

use PedroPCardoso\StartupKit\Core\ValueObjects\Uuid;

final class OrderId
{
    public function __construct(
        private readonly string $value,
    ) {}

    public static function generate(): self
    {
        return new self(Uuid::generate()->value());
    }

    public static function fromString(string $value): self
    {
        return new self(Uuid::fromString($value)->value());
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
