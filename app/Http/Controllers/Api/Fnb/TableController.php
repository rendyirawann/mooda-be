<?php

namespace App\Http\Controllers\Api\Fnb;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

/** Meja & status okupansi. TODO: sambungkan ke `tables` DB bersama. */
class TableController extends Controller
{
    #[OA\Get(
        path: '/fnb/tables',
        tags: ['F&B - Meja'],
        summary: 'Daftar meja + status (kosong/terisi)',
        security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function index(): JsonResponse
    {
        return response()->json(['data' => [
            ['id' => 3, 'name' => 'A3', 'capacity' => 4, 'status' => 'occupied'],
            ['id' => 4, 'name' => 'A4', 'capacity' => 2, 'status' => 'free'],
        ], 'meta' => ['stub' => true]]);
    }
}
