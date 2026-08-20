<?php

namespace App\Http\Controllers\Api\Fnb;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

/**
 * Menu & kategori F&B — data nyata dari DB bersama stakko_pos.
 * Semua query DIFILTER tenant_id milik user login (cegah kebocoran antar-tenant).
 */
class MenuController extends Controller
{
    #[OA\Get(
        path: '/fnb/menus',
        tags: ['F&B - Menu'],
        summary: 'Daftar menu (dengan kategori & harga akhir)',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'category_id', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'q', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'available', in: 'query', required: false, schema: new OA\Schema(type: 'boolean')),
        ],
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function index(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $query = Menu::query()
            ->where('tenant_id', $tenantId)
            ->with('category:id,name');

        if ($request->filled('category_id')) {
            $query->where('category_id', (int) $request->query('category_id'));
        }
        if ($request->filled('q')) {
            $query->where('name', 'ilike', '%'.$request->query('q').'%');
        }
        if ($request->has('available')) {
            $query->where('is_available', $request->boolean('available'));
        }

        $menus = $query->orderBy('name')->get()->map(fn (Menu $m) => [
            'id' => $m->id,
            'name' => $m->name,
            'category_id' => $m->category_id,
            'category' => $m->category?->name,
            'price' => (float) $m->price,
            'discount_percent' => (int) ($m->discount_percent ?? 0),
            'final_price' => $m->final_price,
            'image' => $m->image,
            'available' => (bool) $m->is_available,
        ]);

        return response()->json(['data' => $menus]);
    }

    #[OA\Get(
        path: '/fnb/menus/{id}',
        tags: ['F&B - Menu'],
        summary: 'Detail menu + add-on aktif',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 404, description: 'Tidak ditemukan')]
    )]
    public function show(Request $request, int $id): JsonResponse
    {
        $menu = Menu::query()
            ->where('tenant_id', $request->user()->tenant_id)
            ->with(['category:id,name', 'activeAddons'])
            ->find($id);

        if (! $menu) {
            return response()->json(['message' => 'Menu tidak ditemukan.'], 404);
        }

        return response()->json(['data' => [
            'id' => $menu->id,
            'name' => $menu->name,
            'description' => $menu->description,
            'category' => $menu->category?->name,
            'price' => (float) $menu->price,
            'discount_percent' => (int) ($menu->discount_percent ?? 0),
            'final_price' => $menu->final_price,
            'image' => $menu->image,
            'available' => (bool) $menu->is_available,
            'addons' => $menu->activeAddons->map(fn ($a) => [
                'id' => $a->id,
                'name' => $a->name,
                'price' => (float) $a->price,
            ]),
        ]]);
    }
}
