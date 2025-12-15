<?php

namespace App\Models;

use App\Enums\Symbol;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    /** @use HasFactory<\Database\Factories\AssetFactory> */
    use HasFactory;

    protected function casts()
    {
        return [
            'symbol' => Symbol::class,
        ];
    }
    public function user(){return $this->belongsTo(User::class);}
}
