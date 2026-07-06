<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Orders\Infrastructure;

use PedroPCardoso\StartupKit\Core\Primitives\Errors\DomainError;

final class StorageError extends DomainError
{
    public static function persistFailed(string $detail = ''): self
    {
        return new self(
            code: 'storage.persist_failed',
            message: 'Failed to persist order' . ($detail !== '' ? ': ' . $detail : '.'),
        );
    }

    public static function fetchFailed(string $detail = ''): self
    {
        return new self(
            code: 'storage.fetch_failed',
            message: 'Failed to fetch order' . ($detail !== '' ? ': ' . $detail : '.'),
        );
    }

    public static function deleteFailed(string $detail = ''): self
    {
        return new self(
            code: 'storage.delete_failed',
            message: 'Failed to delete order' . ($detail !== '' ? ': ' . $detail : '.'),
        );
    }

    public static function listFailed(string $detail = ''): self
    {
        return new self(
            code: 'storage.list_failed',
            message: 'Failed to list orders' . ($detail !== '' ? ': ' . $detail : '.'),
        );
    }
}
