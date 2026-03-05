<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::paginate(10);

        return response()->json(['success' => true, 'message' => "Usuários recuperados com sucesso!", 'data' => $users], 200);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $validate = $request->validated();
        User::create($validate);

        return response()->json(['success' => true, 'message' => 'Usuário criado com sucesso!', 'data' => $validate], 201);
    }

    public function show(int $id): JsonResponse
    {
        $users = User::findOrFail($id);

        return response()->json(['success' => true, 'message' => "Usuário encontrado: $id", 'data' => $users], 200);
    }

    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $user = User::findOrFail($id);
        $user->update($validated);

        return response()->json(['success' => true, 'message' => 'Usuário editado com sucesso!', 'data' => $user], 200);
    }
}
