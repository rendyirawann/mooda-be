<?php

namespace App\Http\Controllers\Api\Fnb;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

/**
 * Menu & kategori F&B.
 * TODO: sambungkan ke tabel `menus`/`categories` pada DB bersama stakko_pos.
 */
class MenuController extends Controller
{
    #[OA\Get(
        path: '/fnb/menus',
        tags: ['F&B - Menu'],
        summary: 'Daftar menu (dengan kategori & harga)',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'category_id', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'q', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
        ],
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => [
                ['id' => 1, 'name' => 'Nasi Goreng Spesial', 'category' => 'Makanan', 'price' => 25000, 'available' => true],
                ['id' => 2, 'name' => 'Es Teh Manis', 'category' => 'Minuman', 'price' => 6000, 'available' => true],
            ],
            'meta' => ['stub' => true],
        ]);
    }

    #[OA\Get(
        path: '/fnb/menus/{id}',
        tags: ['F&B - Menu'],
        summary: 'Detail menu + add-on',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 404, description: 'Tidak ditemukan')]
    )]
    public function show(int $id): JsonResponse
    {
        return response()->json([
            'data' => ['id' => $id, 'name' => 'Nasi Goreng Spesial', 'price' => 25000,
                'addons' => [['id' => 10, 'name' => 'Telur', 'price' => 4000]]],
            'meta' => ['stub' => true],
        ]);
    }
}
