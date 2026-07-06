<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Orders\Tests\Unit\Application;

use PedroPCardoso\StartupKit\Core\Contracts\EventBus;

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
