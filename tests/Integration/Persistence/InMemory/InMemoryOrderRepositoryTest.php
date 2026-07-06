<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Orders\Tests\Integration\Persistence\InMemory;

use Cardoso\StartupKit\Orders\Contracts\OrderRepository;
use Cardoso\StartupKit\Orders\Infrastructure\Persistence\InMemory\InMemoryOrderRepository;
use Cardoso\StartupKit\Orders\Tests\Integration\Persistence\OrderRepositoryContractTest;

final class InMemoryOrderRepositoryTest extends OrderRepositoryContractTest
{
    protected function repository(): OrderRepository
    {
        return new InMemoryOrderRepository();
    }
}
