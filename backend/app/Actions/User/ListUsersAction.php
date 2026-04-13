<?php
declare(strict_types=1);

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ListUsersAction
{
    /**
     * Execute the action.
     *
     * Returns the paginated users and the count query (before pagination)
     *
     *
     * @return array{paginator: LengthAwarePaginator, counts: array}
     */
    public function execute(Request $request): array
    {
        $query = User::query()
            ->with(['roles', 'creator:id'])
            ->when(
                $request->filled('search'),
                fn($q) => $q->where(function ($q) use ($request) {
                    $term = strtolower($request->search);

                    $q->whereRaw('LOWER(first_name) LIKE ?', ["%{$term}%"])
                        ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$term}%"])
                        ->orWhereRaw('LOWER(email) LIKE ?', ["%{$term}%"]);
                })
            )
            ->when(
                $request->filled('status'),
                fn($q) => $q->where('is_active', $request->boolean('status'))
            )
            ->orderBy('id', 'asc');

        $countQuery = clone $query;

        $perPage = max(1, min((int) $request->input('per_page', 10), 100));
        $paginator = $query->paginate($perPage);

        return [
            'paginator' => $paginator,
            'counts' => [
                'total' => $paginator->total(),
                'active' => (clone $countQuery)->where('is_active', true)->count(),
                'inactive' => (clone $countQuery)->where('is_active', false)->count(),
            ],
        ];
    }
}