<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

/** Profil tenant & langganan/plan — data nyata dari DB bersama stakko_pos. */
class AccountController extends Controller
{
    #[OA\Get(
        path: '/account/tenant',
        tags: ['Akun'],
        summary: 'Info tenant (bisnis) pengguna',
        security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 404, description: 'Tenant tidak ditemukan')]
    )]
    public function tenant(Request $request): JsonResponse
    {
        $tenant = Tenant::find($request->user()->tenant_id);
        if (! $tenant) {
            return response()->json(['message' => 'Tenant tidak ditemukan.'], 404);
        }

        return response()->json(['data' => [
            'id' => $tenant->id,
            'name' => $tenant->name,
            'slug' => $tenant->slug,
            'vertical' => $tenant->vertical,
            'business_type' => $tenant->business_type,
            'is_active' => (bool) $tenant->is_active,
            'subscription_status' => $tenant->subscription_status,
        ]]);
    }

    #[OA\Get(
        path: '/account/plan',
        tags: ['Akun'],
        summary: 'Langganan & saldo deposit',
        security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function plan(Request $request): JsonResponse
    {
        $tenant = Tenant::find($request->user()->tenant_id);
        if (! $tenant) {
            return response()->json(['message' => 'Tenant tidak ditemukan.'], 404);
        }

        return response()->json(['data' => [
            'plan' => $tenant->plan,
            'billing_mode' => $tenant->billing_mode,
            'deposit_balance' => (float) ($tenant->deposit_points ?? 0),
            'subscription_status' => $tenant->subscription_status,
            'subscription_ends_at' => $tenant->subscription_ends_at,
            'trial_ends_at' => $tenant->trial_ends_at,
        ]]);
    }
}
