<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\User\CreateUserAction;
use App\Actions\User\ListUsersAction;
use App\Actions\User\UpdateUserAction;
use App\Http\Requests\Store\UserRequest as StoreRequest;
use App\Http\Requests\Update\UserRequest as UpdateRequest;
use App\Http\Resources\StoreUpdateUserResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function index(Request $request, ListUsersAction $action): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', User::class);

        $result = $action->execute($request);

        return UserResource::collection($result['paginator'])
            ->additional([
                'counts' => $result['counts'],
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
        Gate::authorize('view', $user);

        $user->load('roles');

        return (new UserResource($user))
            ->additional([
                'success' => true,
                'message' => "User found: {$user->id}",
            ]);
    }

    public function update(UpdateRequest $request, User $user): StoreUpdateUserResource
    {
        $user = UpdateUserAction::new()->execute($user, $request->validated());

        return (new StoreUpdateUserResource($user))
            ->additional([
                'success' => true,
                'message' => 'User updated successfully!',
            ]);
    }

    public function destroy(User $user): UserResource
    {
        Gate::authorize('delete', $user);
        $user->delete();

        $user->load('roles');

        return (new UserResource($user))
            ->additional([
                'success' => true,
                'message' => 'User deleted successfully!',
            ]);
    }
}
