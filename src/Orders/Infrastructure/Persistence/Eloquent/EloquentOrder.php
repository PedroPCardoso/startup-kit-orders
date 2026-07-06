<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Orders\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EloquentOrder extends Model
{
    protected $table = 'orders';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['id', 'customer_id', 'status'];

    public function lines(): HasMany
    {
        return $this->hasMany(EloquentOrderLine::class, 'order_id');
    }
}
