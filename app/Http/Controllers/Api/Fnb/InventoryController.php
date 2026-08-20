<?php

namespace App\Http\Controllers\Api\Fnb;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

/**
 * Inventory: bahan, batch (FEFO), stok masuk/keluar, opname.
 * TODO: sambungkan ke `ingredients`/`ingredient_batches`/`stock_movements` DB bersama.
 */
class InventoryController extends Controller
{
    #[OA\Get(
        path: '/fnb/inventory/ingredients',
        tags: ['F&B - Inventory'],
        summary: 'Daftar bahan + stok tersisa + status minimum',
        security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function ingredients(): JsonResponse
    {
        return response()->json(['data' => [
            ['id' => 1, 'name' => 'Beras', 'unit' => 'kg', 'stock' => 12.5, 'min' => 5, 'low' => false],
            ['id' => 2, 'name' => 'Telur', 'unit' => 'butir', 'stock' => 8, 'min' => 12, 'low' => true],
        ], 'meta' => ['stub' => true]]);
    }

    #[OA\Post(
        path: '/fnb/inventory/movements',
        tags: ['F&B - Inventory'],
        summary: 'Catat stok masuk/keluar (keluar termasuk waste)',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(properties: [
            new OA\Property(property: 'ingredient_id', type: 'integer', example: 1),
            new OA\Property(property: 'type', type: 'string', enum: ['in', 'out'], example: 'in'),
            new OA\Property(property: 'qty', type: 'number', example: 10),
            new OA\Property(property: 'buy_price', type: 'integer', nullable: true, example: 13000, description: 'wajib utk type=in (harga/lot batch)'),
            new OA\Property(property: 'reason', type: 'string', nullable: true, example: 'pembelian / waste / rusak'),
            new OA\Property(property: 'expiry_date', type: 'string', format: 'date', nullable: true),
        ])),
        responses: [new OA\Response(response: 201, description: 'Tercatat')]
    )]
    public function storeMovement(Request $request): JsonResponse
    {
        $request->validate([
            'ingredient_id' => ['required', 'integer'],
            'type' => ['required', 'in:in,out'],
            'qty' => ['required', 'numeric', 'gt:0'],
        ]);

        return response()->json(['data' => ['ok' => true], 'meta' => ['stub' => true]], 201);
    }

    #[OA\Post(
        path: '/fnb/inventory/opname',
        tags: ['F&B - Inventory'],
        summary: 'Stok opname (sesuaikan stok fisik)',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(properties: [
            new OA\Property(property: 'items', type: 'array', items: new OA\Items(properties: [
                new OA\Property(property: 'ingredient_id', type: 'integer'),
                new OA\Property(property: 'counted_qty', type: 'number'),
            ])),
        ])),
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function opname(Request $request): JsonResponse
    {
        return response()->json(['data' => ['adjusted' => 0], 'meta' => ['stub' => true]]);
    }
}
