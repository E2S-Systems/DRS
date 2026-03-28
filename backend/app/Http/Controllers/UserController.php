<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateUserAction;
use App\Http\Requests\Store\UserRequest as StoreRequest;
use App\Http\Requests\Update\UserRequest as UpdateRequest;
use Illuminate\Http\Request;
use App\Http\Resources\StoreUpdateUserResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', User::class);
        $query = User::query()
            ->when(
                $request->filled('search'),
                fn($q) => $q->where(function ($q) use ($request) {
                    $q->where('first_name', 'ilike', "%{$request->search}%")
                        ->orWhere('last_name', 'ilike', "%{$request->search}%")
                        ->orWhere('email', 'ilike', "%{$request->search}%");
                })
            )
            ->when(
                $request->filled('status'),
                fn($q) => $q->where('is_active', $request->status === 'true')
            );

        $users = $query->paginate($request->get('per_page', 10));

        $baseQuery = User::query()->when(
            $request->filled('search'),
            fn($q) => $q->where(function ($q) use ($request) {
                $q->where('first_name', 'ilike', "%{$request->search}%")
                    ->orWhere('last_name', 'ilike', "%{$request->search}%")
                    ->orWhere('email', 'ilike', "%{$request->search}%");
            })
        );

        return UserResource::collection($users)
            ->additional([
                'counts' => [
                    'total' => (clone $baseQuery)->count(),
                    'active' => (clone $baseQuery)->where('is_active', true)->count(),
                    'inactive' => (clone $baseQuery)->where('is_active', false)->count(),
                ],
                'success' => true,
                'message' => 'Users retrieved successfully!',
            ]);
    }

    public function store(StoreRequest $request): StoreUpdateUserResource
    {
        $user = CreateUserAction::new()->execute($request->validated());

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

    public function update(UpdateRequest $request, User $user): StoreUpdateUserResource
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
