<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\User\CreateUserAction;
use App\Actions\User\UpdateUserAction;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\StoreUpdateUserResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Usuários recuperados com sucesso!',
            'data'    => UserResource::collection(User::paginate(10)),
        ]);
    }

    public function store(StoreUserRequest $request, CreateUserAction $action): JsonResponse
    {
        $user = $action->handle($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Usuário criado com sucesso!',
            'data'    => new StoreUpdateUserResource($user),
        ], 201);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => "Usuário encontrado: {$user->id}",
            'data'    => new UserResource($user),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $action): JsonResponse
    {
        $user = $action->handle($user, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Usuário editado com sucesso!',
            'data'    => new StoreUpdateUserResource($user),
        ]);
    }
}