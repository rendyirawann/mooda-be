<?php

namespace App\Http\Controllers\Api\Fnb;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

/**
 * Kitchen Display System. Stok dipotong (FEFO) saat item masuk tahap dapur.
 * TODO: sambungkan ke antrean dapur & StockService pada DB bersama stakko_pos.
 */
class KitchenController extends Controller
{
    #[OA\Get(
        path: '/fnb/kitchen/orders',
        tags: ['F&B - Dapur'],
        summary: 'Antrean dapur (item yang perlu dimasak)',
        security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function queue(): JsonResponse
    {
        return response()->json(['data' => [
            ['order_code' => 'STK-1001', 'item' => 'Nasi Goreng Spesial', 'qty' => 2, 'stage' => 'antre'],
        ], 'meta' => ['stub' => true]]);
    }

    #[OA\Post(
        path: '/fnb/kitchen/items/{id}/bump',
        tags: ['F&B - Dapur'],
        summary: 'Tandai item matang / naik tahap (memicu potong stok FEFO)',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function bump(int $id): JsonResponse
    {
        return response()->json(['data' => ['id' => $id, 'stage' => 'matang', 'stock_deducted' => true], 'meta' => ['stub' => true]]);
    }
}
