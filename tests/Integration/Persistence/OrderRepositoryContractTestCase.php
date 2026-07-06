<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Orders\Tests\Integration\Persistence;

use PedroPCardoso\StartupKit\Core\ValueObjects\Money;
use PedroPCardoso\StartupKit\Orders\Contracts\OrderRepository;
use PedroPCardoso\StartupKit\Orders\Domain\Order;
use PedroPCardoso\StartupKit\Orders\Domain\OrderId;
use PedroPCardoso\StartupKit\Orders\Domain\OrderLine;
use PedroPCardoso\StartupKit\Orders\Domain\OrderStatus;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Abstract contract suite — every OrderRepository driver must pass every test here.
 *
 * Concrete subclasses implement setUp() to provide the repository instance.
 */
abstract class OrderRepositoryContractTestCase extends TestCase
{
    abstract protected function repository(): OrderRepository;

    private function makeLine(string $productId = 'prod-1', int $price = 1000): OrderLine
    {
        return new OrderLine($productId, 'Description', 1, Money::fromInt($price, 'BRL'));
    }

    #[Test]
    public function saves_and_retrieves_order_by_id(): void
    {
        $repo  = $this->repository();
        $id    = OrderId::generate();
        $order = Order::place($id, [$this->makeLine()])->unwrap();

        $repo->save($order);

        $result = $repo->byId($id);
        $this->assertTrue($result->isOk());
        $fetched = $result->unwrap();
        $this->assertSame($id->value(), $fetched->id()->value());
        $this->assertSame(OrderStatus::Confirmed, $fetched->status());
        $this->assertCount(1, $fetched->lines());
    }

    #[Test]
    public function returns_not_found_for_missing_order(): void
    {
        $result = $this->repository()->byId(OrderId::generate());

        $this->assertTrue($result->isErr());
        $this->assertStringStartsWith(
            'not_found',
            $result->match(fn($_) => '', fn($e) => $e->code()),
        );
    }

    #[Test]
    public function update_persists_new_status(): void
    {
        $repo  = $this->repository();
        $id    = OrderId::generate();
        $order = Order::place($id, [$this->makeLine()])->unwrap();
        $repo->save($order);

        $order->markAsPaid();
        $repo->save($order);

        $fetched = $repo->byId($id)->unwrap();
        $this->assertSame(OrderStatus::Paid, $fetched->status());
    }

    #[Test]
    public function delete_removes_order(): void
    {
        $repo  = $this->repository();
        $id    = OrderId::generate();
        $order = Order::place($id, [$this->makeLine()])->unwrap();
        $repo->save($order);

        $repo->delete($order);

        $this->assertTrue($repo->byId($id)->isErr());
    }

    #[Test]
    public function list_returns_all_orders(): void
    {
        $repo = $this->repository();
        $repo->save(Order::place(OrderId::generate(), [$this->makeLine()], 'cust-1')->unwrap());
        $repo->save(Order::place(OrderId::generate(), [$this->makeLine()], 'cust-2')->unwrap());

        $result = $repo->list();
        $this->assertTrue($result->isOk());
        $this->assertCount(2, $result->unwrap());
    }

    #[Test]
    public function list_filters_by_customer(): void
    {
        $repo = $this->repository();
        $repo->save(Order::place(OrderId::generate(), [$this->makeLine()], 'cust-A')->unwrap());
        $repo->save(Order::place(OrderId::generate(), [$this->makeLine()], 'cust-B')->unwrap());

        $result = $repo->list(customerId: 'cust-A');
        $this->assertCount(1, $result->unwrap());
        $this->assertSame('cust-A', $result->unwrap()[0]->customerId());
    }

    #[Test]
    public function list_filters_by_status(): void
    {
        $repo  = $this->repository();
        $id1   = OrderId::generate();
        $order1 = Order::place($id1, [$this->makeLine()])->unwrap();
        $repo->save($order1);

        $order2 = Order::place(OrderId::generate(), [$this->makeLine()])->unwrap();
        $order2->markAsPaid();
        $repo->save($order2);

        $confirmed = $repo->list(status: OrderStatus::Confirmed->value)->unwrap();
        $this->assertCount(1, $confirmed);
        $this->assertSame(OrderStatus::Confirmed, $confirmed[0]->status());
    }
}
