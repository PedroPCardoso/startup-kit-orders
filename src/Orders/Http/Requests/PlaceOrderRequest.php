<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Orders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class PlaceOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'items'                   => 'required|array|min:1',
            'items.*.product_id'      => 'required|string',
            'items.*.description'     => 'required|string',
            'items.*.quantity'        => 'required|integer|min:1',
            'items.*.unit_price'      => 'required|integer|min:0',
            'customer_id'             => 'nullable|string',
        ];
    }
}
