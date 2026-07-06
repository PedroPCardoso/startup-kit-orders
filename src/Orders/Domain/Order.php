<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Orders\Domain;

use Cardoso\StartupKit\Core\Primitives\Errors\ConflictError;
use Cardoso\StartupKit\Core\Primitives\Errors\ValidationError;
use Cardoso\StartupKit\Core\Primitives\Result\Result;
use Cardoso\StartupKit\Core\ValueObjects\Money;

final class Order
{
    /** @var list<OrderLine> */
    private array $lines = [];

    private function __construct(
        private readonly OrderId $id,
        private OrderStatus $status,
        private readonly \DateTimeImmutable $createdAt,
        private readonly ?string $customerId = null,
        private ?\DateTimeImmutable $updatedAt = null,
    ) {}

    public static function draft(OrderId $id, ?string $customerId = null): self
    {
        return new self(
            id: $id,
            status: OrderStatus::Draft,
            createdAt: new \DateTimeImmutable(),
            customerId: $customerId,
        );
    }

    public static function place(OrderId $id, array $lines, ?string $customerId = null): Result
    {
        if (empty($lines)) {
            return Result::err(
                ValidationError::make('lines', 'Order must have at least one item.')
            );
        }

        $order = new self(
            id: $id,
            status: OrderStatus::Confirmed,
            createdAt: new \DateTimeImmutable(),
            customerId: $customerId,
        );

        foreach ($lines as $line) {
            if (!$line instanceof OrderLine) {
                return Result::err(
                    ValidationError::make('lines', 'Each line must be an OrderLine instance.')
                );
            }
            $order->lines[] = $line;
        }

        $order->updatedAt = new \DateTimeImmutable();

        return Result::ok($order);
    }

    public function addLine(OrderLine $line): Result
    {
        if (!$this->status->canTransitionTo(OrderStatus::Confirmed)) {
            return Result::err(
                ConflictError::make('Order', sprintf('Cannot add items in status "%s".', $this->status->value))
            );
        }

        $this->lines[] = $line;
        $this->updatedAt = new \DateTimeImmutable();

        return Result::ok($this);
    }

    public function confirm(): Result
    {
        if (!$this->status->canTransitionTo(OrderStatus::Confirmed)) {
            return Result::err(
                ConflictError::make('Order', sprintf('Cannot confirm order in status "%s".', $this->status->value))
            );
        }

        if (empty($this->lines)) {
            return Result::err(
                ValidationError::make('lines', 'Cannot confirm order without items.')
            );
        }

        $this->status = OrderStatus::Confirmed;
        $this->updatedAt = new \DateTimeImmutable();

        return Result::ok($this);
    }

    public function markAsPaid(): Result
    {
        if (!$this->status->canTransitionTo(OrderStatus::Paid)) {
            return Result::err(
                ConflictError::make('Order', sprintf('Cannot mark as paid in status "%s".', $this->status->value))
            );
        }

        $this->status = OrderStatus::Paid;
        $this->updatedAt = new \DateTimeImmutable();

        return Result::ok($this);
    }

    public function cancel(): Result
    {
        if (!$this->status->canTransitionTo(OrderStatus::Cancelled)) {
            return Result::err(
                ConflictError::make('Order', sprintf('Cannot cancel order in status "%s".', $this->status->value))
            );
        }

        $this->status = OrderStatus::Cancelled;
        $this->updatedAt = new \DateTimeImmutable();

        return Result::ok($this);
    }

    public function id(): OrderId
    {
        return $this->id;
    }

    public function status(): OrderStatus
    {
        return $this->status;
    }

    public function lines(): array
    {
        return $this->lines;
    }

    public function customerId(): ?string
    {
        return $this->customerId;
    }

    public function total(): Money
    {
        $total = Money::zero('BRL');

        foreach ($this->lines as $line) {
            $total = $total->add($line->total());
        }

        return $total;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Reconstitutes an Order from persistent storage without running domain invariants.
     *
     * @param list<OrderLine> $lines
     */
    public static function reconstitute(
        OrderId $id,
        OrderStatus $status,
        array $lines,
        ?string $customerId,
        \DateTimeImmutable $createdAt,
        ?\DateTimeImmutable $updatedAt,
    ): self {
        $order = new self(
            id: $id,
            status: $status,
            createdAt: $createdAt,
            customerId: $customerId,
            updatedAt: $updatedAt,
        );
        $order->lines = $lines;

        return $order;
    }
}
