<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Orders\Tests\Integration\Persistence\InMemory;

use PedroPCardoso\StartupKit\Orders\Contracts\OrderRepository;
use PedroPCardoso\StartupKit\Orders\Infrastructure\Persistence\InMemory\InMemoryOrderRepository;
use PedroPCardoso\StartupKit\Orders\Tests\Integration\Persistence\OrderRepositoryContractTest;

final class InMemoryOrderRepositoryTest extends OrderRepositoryContractTest
{
    protected function repository(): OrderRepository
    {
        return new InMemoryOrderRepository();
    }
}
