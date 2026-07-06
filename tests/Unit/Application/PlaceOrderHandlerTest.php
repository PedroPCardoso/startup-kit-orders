<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Orders\Tests\Unit\Application;

use Cardoso\StartupKit\Core\Primitives\Result\Result;
use Cardoso\StartupKit\Orders\Application\Commands\PlaceOrder;
use Cardoso\StartupKit\Orders\Application\Handlers\PlaceOrderHandler;
use Cardoso\StartupKit\Orders\Contracts\OrderRepository;
use Cardoso\StartupKit\Orders\Domain\OrderId;
use Cardoso\StartupKit\Orders\Domain\OrderStatus;
use Cardoso\StartupKit\Orders\Infrastructure\Persistence\InMemory\InMemoryOrderRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PlaceOrderHandlerTest extends TestCase
{
    private OrderRepository $repository;
    private FakeEventBus $eventBus;
    private PlaceOrderHandler $handler;

    protected function setUp(): void
    {
        $this->repository = new InMemoryOrderRepository();
        $this->eventBus   = new FakeEventBus();
        $this->handler    = new PlaceOrderHandler($this->repository, $this->eventBus);
    }

    #[Test]
    public function places_order_and_returns_order_id(): void
    {
        $command = new PlaceOrder(
            items: [
                ['product_id' => 'prod-1', 'description' => 'Widget', 'quantity' => 2, 'unit_price' => 1000],
            ],
            customerId: 'cust-abc',
        );

        $result = $this->handler->handle($command);

        $this->assertTrue($result->isOk());
        $orderId = $result->unwrap();
        $this->assertInstanceOf(OrderId::class, $orderId);
    }

    #[Test]
    public function persists_order_to_repository(): void
    {
        $command = new PlaceOrder(
            items: [['product_id' => 'p1', 'description' => 'Item', 'quantity' => 1, 'unit_price' => 500]],
        );

        $result  = $this->handler->handle($command);
        $orderId = $result->unwrap();

        $fetched = $this->repository->byId($orderId);
        $this->assertTrue($fetched->isOk());
        $this->assertSame(OrderStatus::Confirmed, $fetched->unwrap()->status());
    }

    #[Test]
    public function publishes_order_placed_event(): void
    {
        $command = new PlaceOrder(
            items: [['product_id' => 'p1', 'description' => 'Item', 'quantity' => 1, 'unit_price' => 500]],
        );

        $this->handler->handle($command);

        $this->assertCount(1, $this->eventBus->published);
    }

    #[Test]
    public function returns_err_when_items_is_empty(): void
    {
        $command = new PlaceOrder(items: []);

        $result = $this->handler->handle($command);

        $this->assertTrue($result->isErr());
    }

    #[Test]
    public function returns_err_when_repository_fails(): void
    {
        $failingRepo = new class implements OrderRepository {
            public function save(\Cardoso\StartupKit\Orders\Domain\Order $order): Result
            {
                return Result::err(new \Cardoso\StartupKit\Orders\Infrastructure\StorageError('storage.fail', 'boom'));
            }
            public function byId(OrderId $id): Result { return Result::ok(null); }
            public function delete(\Cardoso\StartupKit\Orders\Domain\Order $order): Result { return Result::ok(null); }
            public function list(?string $c = null, ?string $s = null): Result { return Result::ok([]); }
        };

        $handler = new PlaceOrderHandler($failingRepo, $this->eventBus);
        $command = new PlaceOrder(
            items: [['product_id' => 'p1', 'description' => 'Item', 'quantity' => 1, 'unit_price' => 500]],
        );

        $result = $handler->handle($command);

        $this->assertTrue($result->isErr());
        $this->assertEmpty($this->eventBus->published);
    }
}
