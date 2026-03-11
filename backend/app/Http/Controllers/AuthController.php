<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\LoginUserAction;
use App\Actions\LogoutUserAction;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\LogoutRequest;
use App\Http\Resources\AuthResource;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        private LoginUserAction $loginUserAction,
        private LogoutUserAction $logoutUserAction,
    ) {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->loginUserAction->execute(
            email: $request->validated('email'),
            password: $request->validated('password'),
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'access_token' => $result['access_token'],
            'token_type' => $result['token_type'],
            'user' => new AuthResource($result['user']),
        ], 201);
    }

    public function logout(LogoutRequest $request): JsonResponse
    {
        $result = $this->logoutUserAction->execute($request->user());

        return response()->json($result, 200);
    }
}
