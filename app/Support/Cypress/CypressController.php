<?php

namespace App\Support\Cypress;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class CypressController
{
    public function create(Request $request)
    {
        $validated = $request->validate([
            'model' => 'required|string',
            'amount' => 'sometimes|numeric',
            'attributes' => 'sometimes|array',
        ]);

        $class = "App\\" . ucfirst(strtolower($validated['model']));

        return $class::factory($validated['amount'] ?? null)
            ->create($validated['attributes'] ?? []);
    }

    public function reset()
    {
        Artisan::call('migrate:fresh');
        Artisan::call('migrate', ['--path' => 'vendor/laravel/telescope/src/Storage/migrations']);
    }

    public function login(Request $request)
    {
        $validated = $request->validate(['attributes' => 'sometimes|array']);

        $user = User::factory()->create($validated['attributes'] ?? []);
        $token = Auth::login($user);

        return ['user' => $user, 'access_token' => $token];
    }
}
