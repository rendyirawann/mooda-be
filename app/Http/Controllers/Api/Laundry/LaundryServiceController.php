<?php

namespace App\Http\Controllers\Api\Laundry;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

/** Master layanan & pelanggan laundry. TODO: `laundry_services`/`laundry_customers`. */
class LaundryServiceController extends Controller
{
    #[OA\Get(
        path: '/laundry/services',
        tags: ['Laundry - Layanan'],
        summary: 'Daftar layanan laundry + tarif/satuan',
        security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function services(): JsonResponse
    {
        return response()->json(['data' => [
            ['id' => 1, 'name' => 'Cuci Kering', 'unit' => 'kg', 'price' => 7000],
            ['id' => 2, 'name' => 'Cuci Setrika', 'unit' => 'kg', 'price' => 10000],
        ], 'meta' => ['stub' => true]]);
    }

    #[OA\Get(
        path: '/laundry/customers',
        tags: ['Laundry - Layanan'],
        summary: 'Daftar pelanggan laundry',
        security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function customers(): JsonResponse
    {
        return response()->json(['data' => [
            ['id' => 5, 'name' => 'Budi', 'phone' => '08123', 'vip' => true],
        ], 'meta' => ['stub' => true]]);
    }
}
