<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                    new OA\Property(property: 'email', type: 'string', example: 'owner@mooda.test'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password'),
                    new OA\Property(property: 'device_name', type: 'string', example: 'mooda-mobile'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Berhasil login', content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'data', properties: [
                        new OA\Property(property: 'token', type: 'string', example: '3|abcd...'),
                        new OA\Property(property: 'user', type: 'object'),
                    ], type: 'object'),
                ]
            )),
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

        if (! Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            throw ValidationException::withMessages([
                'email' => ['Email atau kata sandi salah.'],
            ]);
        }

        $user = $request->user() ?? Auth::user();
        $token = $user->createToken($data['device_name'] ?? 'mooda-mobile')->plainTextToken;

        return response()->json([
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ],
        ]);
    }

    #[OA\Get(
        path: '/auth/me',
        tags: ['Auth'],
        summary: 'Profil pengguna saat ini',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 401, description: 'Belum login'),
        ]
    )]
    public function me(Request $request): JsonResponse
    {
        $u = $request->user();

        return response()->json([
            'data' => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'email_verified_at' => $u->email_verified_at,
            ],
        ]);
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
}
