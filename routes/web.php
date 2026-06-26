<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

// Botón "Publicar" del panel: dispara el rebuild/reexport del sitio estático
// (Astro) en Vercel mediante su deploy hook. Solo para usuarios autenticados.
Route::post('/publish', function () {
    $hook = config('services.prismic.deploy_hook_url');

    if (! $hook) {
        return back()->with('publish_status', 'Falta configurar DEPLOY_HOOK_URL.');
    }

    try {
        $response = Http::timeout(15)->post($hook);

        return back()->with('publish_status', $response->successful()
            ? '✓ Publicación iniciada: el sitio se está reconstruyendo.'
            : 'Error al publicar (código '.$response->status().').');
    } catch (\Throwable $e) {
        return back()->with('publish_status', 'Error al publicar: '.$e->getMessage());
    }
})->middleware('auth')->name('publish');

// ── TEMPORAL: prueba la conexión a R2/S3. Borrar tras diagnosticar. ──
// Visitar: /r2-test?token=TU_ADMIN_API_TOKEN
Route::get('/r2-test', function (Request $request) {
    $token = config('services.admin.token');
    if ($token && ! hash_equals($token, (string) $request->query('token'))) {
        abort(403, 'Token inválido.');
    }

    try {
        Storage::disk('s3')->put('r2-test.txt', 'hola desde zooblog');

        return response()->json([
            'ok'  => true,
            'url' => Storage::disk('s3')->url('r2-test.txt'),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'ok'    => false,
            'class' => get_class($e),
            'error' => $e->getMessage(),
        ], 500);
    }
});
