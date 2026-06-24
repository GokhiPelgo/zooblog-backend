<?php

namespace App\Http\Controllers;

use App\Models\Tutorial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TutorialController extends Controller
{
    /**
     * GET /api/tutorials?lang=es
     * Lista los tutoriales publicados (más recientes primero).
     */
    public function index(Request $request): JsonResponse
    {
        $tutorials = Tutorial::query()
            ->where('is_published', true)
            ->when($request->query('lang'), fn ($q, $lang) => $q->where('lang', $lang))
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->get(['id', 'title', 'slug', 'lang', 'excerpt', 'cover_image', 'level', 'published_at']);

        return response()->json($tutorials);
    }

    /**
     * GET /api/tutorials/{slug}?lang=es
     * Devuelve un tutorial publicado por su slug.
     */
    public function show(string $slug, Request $request): JsonResponse
    {
        $tutorial = Tutorial::query()
            ->where('is_published', true)
            ->where('slug', $slug)
            ->when($request->query('lang'), fn ($q, $lang) => $q->where('lang', $lang))
            ->first();

        if (! $tutorial) {
            return response()->json(['message' => 'Tutorial no encontrado.'], 404);
        }

        return response()->json($tutorial);
    }
}
