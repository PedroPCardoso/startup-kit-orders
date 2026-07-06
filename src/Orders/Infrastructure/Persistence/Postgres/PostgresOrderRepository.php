<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Orders\Infrastructure\Persistence\Postgres;

use PedroPCardoso\StartupKit\Core\Primitives\Errors\NotFoundError;
use PedroPCardoso\StartupKit\Core\Primitives\Result\Result;
use PedroPCardoso\StartupKit\Core\ValueObjects\Money;
use PedroPCardoso\StartupKit\Core\ValueObjects\Uuid;
use PedroPCardoso\StartupKit\Orders\Contracts\OrderRepository;
use PedroPCardoso\StartupKit\Orders\Domain\Order;
use PedroPCardoso\StartupKit\Orders\Domain\OrderId;
use PedroPCardoso\StartupKit\Orders\Domain\OrderLine;
use PedroPCardoso\StartupKit\Orders\Domain\OrderStatus;
use PedroPCardoso\StartupKit\Orders\Infrastructure\Persistence\Eloquent\EloquentOrder;
use PedroPCardoso\StartupKit\Orders\Infrastructure\StorageError;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class PostgresOrderRepository implements OrderRepository
{
    private const CONNECTION = 'pgsql';

    public function save(Order $order): Result
    {
        try {
            $record = EloquentOrder::on(self::CONNECTION)->updateOrCreate(
                ['id' => $order->id()->value()],
                ['customer_id' => $order->customerId(), 'status' => $order->status()->value],
            );

            $record->lines()->delete();

            foreach ($order->lines() as $line) {
                $record->lines()->create([
                    'id'          => Uuid::generate()->value(),
                    'product_id'  => $line->productId(),
                    'description' => $line->description(),
                    'quantity'    => $line->quantity(),
                    'unit_price'  => $line->unitPrice()->amount(),
                    'currency'    => $line->unitPrice()->currency(),
                ]);
            }

            return Result::ok(null);
        } catch (\Throwable $e) {
            return Result::err(StorageError::persistFailed($e->getMessage()));
        }
    }

    public function byId(OrderId $id): Result
    {
        try {
            $record = EloquentOrder::on(self::CONNECTION)->with('lines')->findOrFail($id->value());

            return Result::ok($this->toDomain($record));
        } catch (ModelNotFoundException) {
            return Result::err(NotFoundError::make('Order', $id->value()));
        } catch (\Throwable $e) {
            return Result::err(StorageError::fetchFailed($e->getMessage()));
        }
    }

    public function delete(Order $order): Result
    {
        try {
            EloquentOrder::on(self::CONNECTION)->destroy($order->id()->value());

            return Result::ok(null);
        } catch (\Throwable $e) {
            return Result::err(StorageError::deleteFailed($e->getMessage()));
        }
    }

    public function list(?string $customerId = null, ?string $status = null): Result
    {
        try {
            $query = EloquentOrder::on(self::CONNECTION)->with('lines');

            if ($customerId !== null) {
                $query->where('customer_id', $customerId);
            }

            if ($status !== null) {
                $query->where('status', $status);
            }

            $records = $query->latest()->get();

            return Result::ok($records->map(fn($r) => $this->toDomain($r))->all());
        } catch (\Throwable $e) {
            return Result::err(StorageError::listFailed($e->getMessage()));
        }
    }

    private function toDomain(EloquentOrder $record): Order
    {
        $lines = $record->lines->map(fn($l) => new OrderLine(
            productId: $l->product_id,
            description: $l->description,
            quantity: $l->quantity,
            unitPrice: Money::fromInt($l->unit_price, $l->currency),
        ))->all();

        return Order::reconstitute(
            id: OrderId::fromString($record->id),
            status: OrderStatus::from($record->status),
            lines: $lines,
            customerId: $record->customer_id,
            createdAt: new \DateTimeImmutable($record->created_at->toIso8601String()),
            updatedAt: $record->updated_at
                ? new \DateTimeImmutable($record->updated_at->toIso8601String())
                : null,
        );
    }
}
