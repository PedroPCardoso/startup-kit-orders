<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Orders\Tests\Unit\Application;

use Cardoso\StartupKit\Core\ValueObjects\Money;
use Cardoso\StartupKit\Orders\Application\Handlers\GetOrderByIdHandler;
use Cardoso\StartupKit\Orders\Application\Queries\GetOrderById;
use Cardoso\StartupKit\Orders\Domain\Order;
use Cardoso\StartupKit\Orders\Domain\OrderId;
use Cardoso\StartupKit\Orders\Domain\OrderLine;
use Cardoso\StartupKit\Orders\Infrastructure\Persistence\InMemory\InMemoryOrderRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GetOrderByIdHandlerTest extends TestCase
{
    private InMemoryOrderRepository $repository;
    private GetOrderByIdHandler $handler;

    protected function setUp(): void
    {
        $this->repository = new InMemoryOrderRepository();
        $this->handler    = new GetOrderByIdHandler($this->repository);
    }

    #[Test]
    public function returns_order_by_id(): void
    {
        $id    = OrderId::generate();
        $line  = new OrderLine('prod-1', 'Widget', 1, Money::fromInt(500, 'BRL'));
        $order = Order::place($id, [$line])->unwrap();
        $this->repository->save($order);

        $result = $this->handler->handle(new GetOrderById($id->value()));

        $this->assertTrue($result->isOk());
        $this->assertSame($id->value(), $result->unwrap()->id()->value());
    }

    #[Test]
    public function returns_not_found_for_unknown_id(): void
    {
        $unknownId = OrderId::generate();

        $result = $this->handler->handle(new GetOrderById($unknownId->value()));

        $this->assertTrue($result->isErr());
        $this->assertStringStartsWith('not_found', $result->match(fn($_) => '', fn($e) => $e->code()));
    }

    #[Test]
    public function returns_err_for_invalid_uuid(): void
    {
        $result = $this->handler->handle(new GetOrderById('not-a-uuid'));

        $this->assertTrue($result->isErr());
    }
}
