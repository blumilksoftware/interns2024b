<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\SortHelper;
use App\Models\School;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class SchoolsAPIController extends Controller
{
    public function index(SortHelper $sorter): JsonResponse
    {
        $query = $sorter->sort(School::query(), ["id", "name", "regon", "updated_at", "created_at"], ["students", "address"]);
        $query = $this->sortByStudents($query, $sorter);
        $query = $this->sortByAddress($query, $sorter);
        $query = $sorter->search($query, "name");
        $schools = $sorter->paginate($query);

        return response()->json($schools);
    }

    private function sortByStudents(Builder $query, SortHelper $sorter): Builder
    {
        [$field, $order] = $sorter->getSortParameters();

        if ($field === "students") {
            return $query->withCount("users")->orderBy("users_count", $order);
        }

        return $query;
    }

    private function sortByAddress(Builder $query, SortHelper $sorter): Builder
    {
        [$field, $order] = $sorter->getSortParameters();

        if ($field === "address") {
            return $query->orderBy("city", $order)
                ->orderBy("zip_code", $order)
                ->orderBy("street", $order)
                ->orderBy("name", $order);
        }

        return $query;
    }
}
