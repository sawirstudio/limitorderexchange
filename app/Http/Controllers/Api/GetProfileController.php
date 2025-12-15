<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class GetProfileController extends Controller
{
    public function __invoke(Request $request)
    {
        return (fn (): User => $request->user())()
            ->loadSum(relations: ['assets' => function ($query) {
                return $query->whereNull('locked_amount');
            }], column: 'amount')
            ->toResource();
    }
}
