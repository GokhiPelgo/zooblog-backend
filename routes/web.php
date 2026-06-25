<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ── TEMPORAL: crea/confirma el usuario admin. Borrar tras usarlo. ──
// Visitar: /setup-admin?token=TU_ADMIN_API_TOKEN
Route::get('/setup-admin', function (Request $request) {
    $token = config('services.admin.token');

    if (! $token || ! hash_equals($token, (string) $request->query('token'))) {
        abort(403, 'Token inválido.');
    }

    $user = \App\Models\User::updateOrCreate(
        ['email' => 'chelo@zooblog.com'],
        ['name' => 'Admin', 'password' => Hash::make('password123')]
    );

    return response()->json([
        'ok'           => true,
        'email'        => $user->email,
        'just_created' => $user->wasRecentlyCreated,
        'total_users'  => \App\Models\User::count(),
    ]);
});
