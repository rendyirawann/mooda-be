<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class MetaController extends Controller
{
    #[OA\Get(
        path: '/health',
        tags: ['Umum'],
        summary: 'Health check',
        responses: [new OA\Response(response: 200, description: 'Service sehat', content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'ok'),
                new OA\Property(property: 'service', type: 'string', example: 'mooda-be'),
            ]
        ))]
    )]
    public function health(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'service' => 'mooda-be',
            'version' => '1.0.0',
        ]);
    }

    #[OA\Get(
        path: '/config',
        tags: ['Umum'],
        summary: 'Konfigurasi publik untuk klien mobile',
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function config(): JsonResponse
    {
        return response()->json([
            'data' => [
                'app_name' => config('app.name'),
                'currency' => 'IDR',
                'verticals' => ['fnb', 'laundry', 'event'],
                'min_app_version' => '1.0.0',
            ],
        ]);
    }
}
