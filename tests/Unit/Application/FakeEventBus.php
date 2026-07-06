<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Orders\Tests\Unit\Application;

use Cardoso\StartupKit\Core\Contracts\EventBus;

final class FakeEventBus implements EventBus
{
    /** @var list<object> */
    public array $published = [];

    public function publish(object $event): void
    {
        $this->published[] = $event;
    }

    public function subscribe(string $eventClass, callable $handler): void {}
}
