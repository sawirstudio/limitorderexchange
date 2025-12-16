<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePersonalAccessTokenRequest;
use App\Http\Resources\PersonalAccessTokenResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PersonalAccessTokenController extends Controller
{
    public function store(CreatePersonalAccessTokenRequest $request)
    {
        $user = User::query()->where('email', $request->email)->first();
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }
        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        };

        return new PersonalAccessTokenResource($user->createToken('web'));
    }

    public function destroy(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }
}
