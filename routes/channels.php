<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('private-user.{id}', function ($user, $id) {
    return ((int) $user->getKey()) === ((int) $id);
});
