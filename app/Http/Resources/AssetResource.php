<?php

namespace App\Http\Resources;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Asset $resource
 */
class AssetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'symbol' => $this->resource->symbol->value,
            'amount' => $this->resource->amount,
            'locked_amount' => $this->resource->locked_amount,
        ];
    }
}
