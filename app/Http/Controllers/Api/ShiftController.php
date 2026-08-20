<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

/** Shift kasir (buka/tutup + kas). TODO: `shifts` DB bersama stakko_pos. */
class ShiftController extends Controller
{
    #[OA\Get(
        path: '/shifts/current',
        tags: ['Shift'],
        summary: 'Shift aktif saat ini',
        security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 404, description: 'Belum buka shift')]
    )]
    public function current(): JsonResponse
    {
        return response()->json(['data' => ['id' => 7, 'opened_at' => '2026-08-20T08:00:00Z', 'opening_cash' => 200000], 'meta' => ['stub' => true]]);
    }

    #[OA\Post(
        path: '/shifts/open',
        tags: ['Shift'],
        summary: 'Buka shift',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(properties: [
            new OA\Property(property: 'opening_cash', type: 'integer', example: 200000),
        ])),
        responses: [new OA\Response(response: 201, description: 'Dibuka')]
    )]
    public function open(Request $request): JsonResponse
    {
        return response()->json(['data' => ['id' => 8, 'status' => 'open'], 'meta' => ['stub' => true]], 201);
    }

    #[OA\Post(
        path: '/shifts/close',
        tags: ['Shift'],
        summary: 'Tutup shift (rekap kas)',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(properties: [
            new OA\Property(property: 'closing_cash', type: 'integer', example: 950000),
        ])),
        responses: [new OA\Response(response: 200, description: 'Ditutup')]
    )]
    public function close(Request $request): JsonResponse
    {
        return response()->json(['data' => ['status' => 'closed', 'expected' => 950000, 'diff' => 0], 'meta' => ['stub' => true]]);
    }
}
