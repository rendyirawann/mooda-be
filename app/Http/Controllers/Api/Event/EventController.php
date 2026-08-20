<?php

namespace App\Http\Controllers\Api\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

/**
 * Vertical Event (roadmap) — contoh bahwa mooda-be menampung banyak vertical,
 * bukan hanya POS F&B. Tag dipisah agar Swagger tetap rapi.
 */
class EventController extends Controller
{
    #[OA\Get(
        path: '/event/events',
        tags: ['Event'],
        summary: 'Daftar event',
        security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function index(): JsonResponse
    {
        return response()->json(['data' => [
            ['id' => 1, 'name' => 'Konser Mooda Fest', 'date' => '2026-09-01', 'tickets_sold' => 320, 'quota' => 500],
        ], 'meta' => ['stub' => true, 'roadmap' => true]]);
    }
}
