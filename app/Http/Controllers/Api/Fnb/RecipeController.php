<?php

namespace App\Http\Controllers\Api\Fnb;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

/** Resep menu (bahan per menu) untuk HPP. TODO: `menu_ingredients` DB bersama. */
class RecipeController extends Controller
{
    #[OA\Get(
        path: '/fnb/recipes/{menuId}',
        tags: ['F&B - Resep & HPP'],
        summary: 'Resep sebuah menu + HPP saat ini',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'menuId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function show(int $menuId): JsonResponse
    {
        return response()->json(['data' => [
            'menu_id' => $menuId,
            'ingredients' => [
                ['ingredient_id' => 1, 'name' => 'Beras', 'qty' => 0.2, 'unit' => 'kg'],
                ['ingredient_id' => 2, 'name' => 'Telur', 'qty' => 1, 'unit' => 'butir'],
            ],
            'hpp' => 8500,
            'price' => 25000,
            'margin_pct' => 66,
        ], 'meta' => ['stub' => true]]);
    }
}
