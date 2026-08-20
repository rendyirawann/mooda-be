<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: '/auth/login',
        tags: ['Auth'],
        summary: 'Login & terbitkan Bearer token',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', example: 'owner@warung.test'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password'),
                    new OA\Property(property: 'device_name', type: 'string', example: 'mooda-mobile'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Berhasil login'),
            new OA\Response(response: 403, description: 'Akun nonaktif / diblokir'),
            new OA\Response(response: 422, description: 'Kredensial tidak valid'),
        ]
    )]
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:120'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], (string) $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau kata sandi salah.'],
            ]);
        }

        if (! $user->canUseApp()) {
            return response()->json([
                'message' => 'Akun Anda nonaktif atau diblokir. Hubungi admin.',
            ], 403);
        }

        $token = $user->createToken($data['device_name'] ?? 'mooda-mobile')->plainTextToken;

        return response()->json([
            'data' => [
                'token' => $token,
                'user' => $this->userPayload($user),
            ],
        ]);
    }

    #[OA\Get(
        path: '/auth/me',
        tags: ['Auth'],
        summary: 'Profil pengguna saat ini + tenant',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 401, description: 'Belum login'),
        ]
    )]
    public function me(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->userPayload($request->user())]);
    }

    #[OA\Post(
        path: '/auth/logout',
        tags: ['Auth'],
        summary: 'Logout (hapus token saat ini)',
        security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'Berhasil logout')]
    )]
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Berhasil logout.']);
    }

    private function userPayload(User $user): array
    {
        $tenant = $user->tenant_id ? Tenant::find($user->tenant_id) : null;

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'tenant' => $tenant ? [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'vertical' => $tenant->vertical,
                'plan' => $tenant->plan,
            ] : null,
        ];
    }
}
