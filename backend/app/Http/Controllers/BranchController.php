<?php

namespace App\Http\Controllers;

use App\Http\Requests\Store\BranchRequest as StoreRequest;
use App\Http\Requests\Update\BranchRequest as UpdateRequest;
use App\Http\Resources\BranchResource;
use App\Models\Branch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class BranchController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Branch::class);

        $branches = Branch::query()
            ->orderBy('name')
            ->paginate(10);

        return BranchResource::collection($branches);
    }

    public function store(StoreRequest $request): BranchResource
    {
        $branch = Branch::create($request->validated());

        return new BranchResource($branch);
    }

    public function show(Branch $branch): BranchResource
    {
        Gate::authorize('view', $branch);

        return new BranchResource($branch);
    }

    public function update(UpdateRequest $request, Branch $branch): BranchResource
    {
        $branch->update($request->validated());

        return new BranchResource($branch);
    }

    public function destroy(Branch $branch): JsonResponse
    {
        Gate::authorize('delete', $branch);

        $branch->delete();

        return response()->json(null, 204);
    }
}
