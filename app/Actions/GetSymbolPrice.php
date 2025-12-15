<?php

namespace App\Actions;

use App\Enums\Symbol;

class GetSymbolPrice
{
    public function __invoke(Symbol $symbol): int|float
    {
        return 100000;
    }
}
