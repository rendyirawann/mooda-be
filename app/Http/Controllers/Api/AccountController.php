<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

/** Profil tenant & langganan/plan. TODO: `tenants`/`subscriptions` DB bersama. */
class AccountController extends Controller
{
    #[OA\Get(
        path: '/account/tenant',
        tags: ['Akun'],
        summary: 'Info tenant (bisnis) pengguna',
        security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function tenant(): JsonResponse
    {
        return response()->json(['data' => [
            'id' => 1, 'name' => 'Warung Mooda', 'vertical' => 'fnb', 'subdomain' => 'warung',
        ], 'meta' => ['stub' => true]]);
    }

    #[OA\Get(
        path: '/account/plan',
        tags: ['Akun'],
        summary: 'Langganan & saldo deposit',
        security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function plan(): JsonResponse
    {
        return response()->json(['data' => [
            'plan' => 'starter', 'billing_mode' => 'deposit', 'deposit_balance' => 2000,
            'features' => ['pos', 'inventory', 'hpp'],
        ], 'meta' => ['stub' => true]]);
    }
}
