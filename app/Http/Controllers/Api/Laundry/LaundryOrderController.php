<?php

namespace App\Http\Controllers\Api\Laundry;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

/**
 * Laundry: nota & pipeline produksi.
 * TODO: sambungkan ke `laundry_orders`/`laundry_order_items`/`laundry_status_logs`.
 */
class LaundryOrderController extends Controller
{
    #[OA\Get(
        path: '/laundry/orders',
        tags: ['Laundry - Kasir'],
        summary: 'Daftar nota laundry',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'string'))],
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function index(): JsonResponse
    {
        return response()->json(['data' => [
            ['id' => 1, 'invoice' => 'LDR-0001', 'customer' => 'Budi', 'stage' => 'Dicuci', 'payment' => 'nanti', 'total' => 24000],
        ], 'meta' => ['stub' => true]]);
    }

    #[OA\Post(
        path: '/laundry/orders',
        tags: ['Laundry - Kasir'],
        summary: 'Buat nota laundry (harga server-side, VIP diskon)',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(properties: [
            new OA\Property(property: 'customer_id', type: 'integer', example: 5),
            new OA\Property(property: 'delivery', type: 'string', enum: ['pickup', 'antar'], example: 'pickup'),
            new OA\Property(property: 'items', type: 'array', items: new OA\Items(properties: [
                new OA\Property(property: 'service_id', type: 'integer', example: 2),
                new OA\Property(property: 'qty', type: 'number', example: 3.5, description: 'kg/pcs/pasang sesuai satuan layanan'),
            ])),
        ])),
        responses: [new OA\Response(response: 201, description: 'Dibuat')]
    )]
    public function store(Request $request): JsonResponse
    {
        $request->validate(['items' => ['required', 'array', 'min:1']]);

        return response()->json(['data' => ['id' => 2, 'invoice' => 'LDR-0002', 'stage' => 'Diterima'], 'meta' => ['stub' => true]], 201);
    }

    #[OA\Post(
        path: '/laundry/orders/{id}/advance',
        tags: ['Laundry - Produksi'],
        summary: 'Naikkan status ke tahap berikutnya',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function advance(int $id): JsonResponse
    {
        return response()->json(['data' => ['id' => $id, 'stage' => 'Dikeringkan'], 'meta' => ['stub' => true]]);
    }
}
