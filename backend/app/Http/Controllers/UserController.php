<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\StoreUpdateUserResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', User::class);
        $users = User::query()->paginate(10);

        return UserResource::collection($users)
            ->additional([
                'success' => true,
                'message' => 'Users retrieved successfully!',
            ]);
    }

    public function store(StoreUserRequest $request): StoreUpdateUserResource
    {   
        $user = User::create($request->validated());

        return (new StoreUpdateUserResource($user))
            ->additional([
                'success' => true,
                'message' => 'User created successfully!',
            ]);
    }

    public function show(User $user): UserResource
    {
        Gate::authorize('view', User::class);
        return (new UserResource($user))
            ->additional([
                'success' => true,
                'message' => "User found: {$user->id}",
            ]);
    }

    public function update(UpdateUserRequest $request, User $user): StoreUpdateUserResource
    {
        $validated = $request->validated();
        $user->update($validated);

        return (new StoreUpdateUserResource($user))
            ->additional([
                'success' => true,
                'message' => 'User updated successfully!',
            ]);
    }

    public function destroy(User $user): UserResource
    {
        Gate::authorize('delete', User::class);
        $user->delete();

        return (new UserResource($user))
            ->additional([
                'success' => true,
                'message' => 'User deleted successfully!',
            ]);
    }
}