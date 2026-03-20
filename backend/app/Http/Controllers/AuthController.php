<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\LoginUserAction;
use App\Actions\LogoutUserAction;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\LogoutRequest;
use App\Http\Resources\AuthResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $result = (new LoginUserAction())->execute(
            email: $request->validated('email'),
            password: $request->validated('password'),
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
            ], HttpResponse::HTTP_UNAUTHORIZED);
        }

        return response()->json([
            'success' => true,
            'token' => $result['access_token'],
            'token_type' => $result['token_type'],
        ], HttpResponse::HTTP_OK);
    }

    public function logout(LogoutRequest $request): JsonResponse
    {
        $result = LogoutUserAction::new()->execute($request->user());

        return response()->json($result, HttpResponse::HTTP_OK);
    }
}
