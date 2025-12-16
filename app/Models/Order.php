<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\Symbol;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected function casts()
    {
        return [
            'side' => 'boolean',
            'symbol' => Symbol::class,
            'status' => OrderStatus::class,
        ];
    }

    public function user(){return $this->belongsTo(User::class);}
    public function trade(){return $this->belongsTo(Trade::class);}
}
