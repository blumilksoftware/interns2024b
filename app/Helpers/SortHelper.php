<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Lang;
use Symfony\Component\HttpFoundation\Response as Status;

class SortHelper
{
    public function __construct(
        private Request $request,
    ) {}

    /**
     * @param array<string> $allowedFields
     * @param array<string> $ignoredFields
     */
    public function sort(Builder $query, array $allowedFields, array $ignoredFields): Builder
    {
        [$field, $order] = $this->getSortParameters();

        if (!in_array($field, $allowedFields, true)) {
            if (in_array($field, $ignoredFields, true)) {
                return $query;
            }

            abort(Status::HTTP_BAD_REQUEST, Lang::get("validation.custom.sorting.unsupported_field", ["attribute" => $field]));
        }

        return $query->orderBy($field, $order);
    }

    /**
     * @return array<string>
     */
    public function getSortParameters(): array
    {
        $field = $this->request->query("sort", "id");
        $ascending = $this->request->query("order", "desc") === "asc";

        return [$field, $ascending ? "asc" : "desc"];
    }

    public function search(Builder $query, string $field): Builder
    {
        $searchText = $this->request->query("search");

        if ($searchText) {
            return $query->where($field, "ilike", "%$searchText%");
        }

        return $query;
    }

    public function paginate(Builder $query): LengthAwarePaginator
    {
        $limit = (int)$this->request->query("limit", "50");

        if (!$limit || $limit < 0) {
            $limit = 50;
        }

        return $query->paginate($limit);
    }
}
