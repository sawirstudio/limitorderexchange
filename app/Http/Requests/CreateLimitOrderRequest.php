<?php

namespace App\Http\Requests;

use App\Enums\Symbol;
use App\Models\Asset;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CreateLimitOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'side' => ['required', 'boolean'],
            'symbol' => ['required', Rule::enum(Symbol::class)],
            'amount' => ['required', Rule::numeric()
                ->greaterThan(0)
                ->max($this->boolean('side')
                ? $this->user()->balance
                : $this->assets()
                    ->where('symbol', $this->enum('symbol', Symbol::class))
                    ->value('amount', 0)),
            ],
        ];
    }

    public function assets()
    {
        return once(fn () => Asset::query()
            ->whereBelongsTo($this->user())
            ->get());
    }
}
