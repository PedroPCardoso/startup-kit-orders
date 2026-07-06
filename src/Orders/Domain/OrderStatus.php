<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Orders\Domain;

enum OrderStatus: string
{
    case Draft = 'draft';
    case Confirmed = 'confirmed';
    case Processing = 'processing';
    case Paid = 'paid';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Draft => in_array($target, [self::Confirmed, self::Cancelled], true),
            self::Confirmed => in_array($target, [self::Processing, self::Paid, self::Cancelled], true),
            self::Processing => in_array($target, [self::Paid, self::Cancelled], true),
            self::Paid => in_array($target, [self::Shipped, self::Refunded], true),
            self::Shipped => in_array($target, [self::Delivered], true),
            self::Delivered => in_array($target, [self::Refunded], true),
            self::Cancelled, self::Refunded => false,
        };
    }
}
