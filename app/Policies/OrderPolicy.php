<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    public function cancel(User $user, Order $order): Response
    {
        if ($user->getKey() == $order->user_id) {
            return Response::allow();
        }

        return Response::denyAsNotFound();
    }
}
