<?php

namespace App\Http\Controllers\Api\Fnb;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

/** Laporan penjualan & HPP. TODO: agregasi dari DB bersama stakko_pos. */
class ReportController extends Controller
{
    #[OA\Get(
        path: '/fnb/reports/sales',
        tags: ['F&B - Laporan'],
        summary: 'Ringkasan penjualan (per periode)',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'from', in: 'query', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'to', in: 'query', schema: new OA\Schema(type: 'string', format: 'date')),
        ],
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function sales(): JsonResponse
    {
        return response()->json(['data' => [
            'gross' => 1250000, 'net' => 1125000, 'orders' => 48, 'avg_ticket' => 26041,
        ], 'meta' => ['stub' => true]]);
    }

    #[OA\Get(
        path: '/fnb/reports/hpp',
        tags: ['F&B - Laporan'],
        summary: 'Dashboard HPP (food cost & margin)',
        security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function hpp(): JsonResponse
    {
        return response()->json(['data' => [
            'revenue' => 1250000, 'hpp' => 437500, 'food_cost_pct' => 35, 'margin_pct' => 65,
            'top_low_margin' => [['menu' => 'Kopi Susu', 'margin_pct' => 42]],
        ], 'meta' => ['stub' => true]]);
    }
}
