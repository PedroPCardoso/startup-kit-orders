<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Orders\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

class EloquentOrderLine extends Model
{
    protected $table = 'order_lines';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['id', 'order_id', 'product_id', 'description', 'quantity', 'unit_price', 'currency'];
}
