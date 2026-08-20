<?php

namespace App\Http\Controllers\Api\Fnb;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

/**
 * Kasir F&B: pesanan & pembayaran (harga dihitung server-side).
 * TODO: sambungkan ke `orders`/`order_details` DB bersama stakko_pos.
 */
class OrderController extends Controller
{
    #[OA\Get(
        path: '/fnb/orders',
        tags: ['F&B - Kasir'],
        summary: 'Daftar pesanan (filter status/tanggal)',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'status', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['open', 'paid', 'void'])),
        ],
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function index(): JsonResponse
    {
        return response()->json(['data' => [
            ['id' => 1001, 'code' => 'STK-1001', 'table' => 'A3', 'total' => 56000, 'status' => 'open'],
        ], 'meta' => ['stub' => true]]);
    }

    #[OA\Post(
        path: '/fnb/orders',
        tags: ['F&B - Kasir'],
        summary: 'Buat pesanan baru',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            required: ['items'],
            properties: [
                new OA\Property(property: 'table_id', type: 'integer', nullable: true, example: 3),
                new OA\Property(property: 'order_type', type: 'string', enum: ['dine_in', 'take_away'], example: 'dine_in'),
                new OA\Property(property: 'items', type: 'array', items: new OA\Items(properties: [
                    new OA\Property(property: 'menu_id', type: 'integer', example: 1),
                    new OA\Property(property: 'qty', type: 'integer', example: 2),
                    new OA\Property(property: 'notes', type: 'string', example: 'pedas'),
                    new OA\Property(property: 'addon_ids', type: 'array', items: new OA\Items(type: 'integer')),
                ])),
            ]
        )),
        responses: [new OA\Response(response: 201, description: 'Dibuat'), new OA\Response(response: 422, description: 'Validasi gagal')]
    )]
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'integer'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        return response()->json(['data' => ['id' => 1002, 'code' => 'STK-1002', 'status' => 'open', 'total' => 0],
            'meta' => ['stub' => true]], 201);
    }

    #[OA\Get(
        path: '/fnb/orders/{id}',
        tags: ['F&B - Kasir'],
        summary: 'Detail pesanan (+ item & struk)',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function show(int $id): JsonResponse
    {
        return response()->json(['data' => ['id' => $id, 'items' => [], 'subtotal' => 0, 'tax' => 0, 'total' => 0],
            'meta' => ['stub' => true]]);
    }

    #[OA\Post(
        path: '/fnb/orders/{id}/pay',
        tags: ['F&B - Kasir'],
        summary: 'Bayar pesanan (cash/QRIS)',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(properties: [
            new OA\Property(property: 'method', type: 'string', enum: ['cash', 'qris'], example: 'cash'),
            new OA\Property(property: 'paid', type: 'integer', example: 60000),
        ])),
        responses: [new OA\Response(response: 200, description: 'Terbayar')]
    )]
    public function pay(int $id, Request $request): JsonResponse
    {
        return response()->json(['data' => ['id' => $id, 'status' => 'paid', 'change' => 4000], 'meta' => ['stub' => true]]);
    }
}
