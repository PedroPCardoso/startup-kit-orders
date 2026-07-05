<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Orders\Tests\Unit\Domain;

use Cardoso\StartupKit\Core\ValueObjects\Money;
use Cardoso\StartupKit\Orders\Domain\Order;
use Cardoso\StartupKit\Orders\Domain\OrderId;
use Cardoso\StartupKit\Orders\Domain\OrderLine;
use Cardoso\StartupKit\Orders\Domain\OrderStatus;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class OrderTest extends TestCase
{
    #[Test]
    public function place_order_with_items(): void
    {
        $id = OrderId::generate();
        $line = new OrderLine('prod-1', 'Product 1', 2, Money::fromInt(1000, 'BRL'));

        $result = Order::place($id, [$line], 'cust-1');

        $this->assertTrue($result->isOk());
        $order = $result->unwrap();
        $this->assertSame(OrderStatus::Confirmed, $order->status());
        $this->assertCount(1, $order->lines());
    }

    #[Test]
    public function place_order_without_items_returns_err(): void
    {
        $id = OrderId::generate();

        $result = Order::place($id, [], 'cust-1');

        $this->assertTrue($result->isErr());
    }

    #[Test]
    public function draft_order_starts_as_draft(): void
    {
        $id = OrderId::generate();
        $order = Order::draft($id);

        $this->assertSame(OrderStatus::Draft, $order->status());
    }

    #[Test]
    public function confirm_draft_order(): void
    {
        $id = OrderId::generate();
        $order = Order::draft($id);
        $order->addLine(new OrderLine('prod-1', 'P1', 1, Money::fromInt(500, 'BRL')));

        $result = $order->confirm();

        $this->assertTrue($result->isOk());
        $this->assertSame(OrderStatus::Confirmed, $order->status());
    }

    #[Test]
    public function confirm_empty_order_returns_err(): void
    {
        $id = OrderId::generate();
        $order = Order::draft($id);

        $result = $order->confirm();

        $this->assertTrue($result->isErr());
    }

    #[Test]
    public function cancel_order(): void
    {
        $id = OrderId::generate();
        $line = new OrderLine('prod-1', 'P1', 1, Money::fromInt(500, 'BRL'));

        $order = Order::place($id, [$line])->unwrap();

        $result = $order->cancel();
        $this->assertTrue($result->isOk());
        $this->assertSame(OrderStatus::Cancelled, $order->status());
    }

    #[Test]
    public function cannot_cancel_cancelled_order(): void
    {
        $id = OrderId::generate();
        $line = new OrderLine('prod-1', 'P1', 1, Money::fromInt(500, 'BRL'));

        $order = Order::place($id, [$line])->unwrap();
        $order->cancel();

        $result = $order->cancel();
        $this->assertTrue($result->isErr());
    }

    #[Test]
    public function total_calculation(): void
    {
        $id = OrderId::generate();
        $lines = [
            new OrderLine('prod-1', 'P1', 2, Money::fromInt(1000, 'BRL')),
            new OrderLine('prod-2', 'P2', 3, Money::fromInt(500, 'BRL')),
        ];

        $order = Order::place($id, $lines)->unwrap();

        $total = $order->total();
        $this->assertSame(3500, $total->amount());
    }

    #[Test]
    public function order_line_with_zero_quantity_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new OrderLine('prod-1', 'P1', 0, Money::fromInt(100, 'BRL'));
    }

    #[Test]
    public function mark_as_paid(): void
    {
        $id = OrderId::generate();
        $line = new OrderLine('prod-1', 'P1', 1, Money::fromInt(1000, 'BRL'));

        $order = Order::place($id, [$line])->unwrap();

        $result = $order->markAsPaid();
        $this->assertTrue($result->isOk());
        $this->assertSame(OrderStatus::Paid, $order->status());
    }
}
