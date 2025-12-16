<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Order $resource
 */
class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'side' => $this->resource->side,
            'symbol' => $this->resource->symbol,
            'price' => $this->resource->price,
            'amount' => $this->resource->amount,
            'status' => $this->resource->status->value,
            'created_at' => $this->resource->created_at,
        ];
    }
}
