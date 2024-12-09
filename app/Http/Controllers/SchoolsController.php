<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\Voivodeship;
use App\Helpers\SortHelper;
use App\Http\Requests\SchoolRequest;
use App\Http\Resources\SchoolResource;
use App\Jobs\FetchSchoolsJob;
use App\Models\School;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as Status;
use Throwable;

use function collect;
use function config;

class SchoolsController extends Controller
{
    public function index(SortHelper $sorter, Request $request): Response
    {
        $query = School::query()->where("is_admin_school", false);
        $query = $sorter->sort($query, ["id", "name", "regon", "updated_at", "created_at"], ["students", "address"]);
        $query = $this->sortByStudents($query, $sorter);
        $query = $this->sortByAddress($query, $sorter);
        $query = $this->filterDisabledSchools($query, $request);
        $query = $sorter->search($query, "name");

        return Inertia::render("Admin/SchoolsPanel", [
            "schools" => SchoolResource::collection($sorter->paginate($query)),
        ]);
    }

    public function store(SchoolRequest $request): RedirectResponse
    {
        School::query()->create($request->validated());

        return redirect()
            ->back()
            ->with("status", "Szkoła została dodana.");
    }

    public function update(SchoolRequest $request, School $school): RedirectResponse
    {
        $school->update($request->validated());

        return redirect()
            ->back();
    }

    public function destroy(School $school): RedirectResponse
    {
        $school->delete();

        return redirect()
            ->back()
            ->with("status", "Szkoła została usunięta.");
    }

    public function disable(School $school): RedirectResponse
    {
        return $this->toggleDisable($school, false);
    }

    public function enable(School $school): RedirectResponse
    {
        return $this->toggleDisable($school, true);
    }

    public function toggleDisable(School $school, bool $value): RedirectResponse
    {
        $school->is_disabled = $value;
        $school->save();

        $message = $value ? "Szkoła została zablokowana." : "Szkoła została odblokowana.";

        return redirect()
            ->back()
            ->with("status", $message);
    }

    /**
     * @throws Throwable
     */
    public function fetch(): JsonResponse
    {
        if ($this->isFetching()) {
            return response()->json(["message" => "Pobieranie w toku, proszę czekać"], Status::HTTP_CONFLICT);
        }

        $voivodeships = collect(config("schools.voivodeships"));
        $schoolTypes = config("schools.types");
        $jobs = $voivodeships->map(fn(Voivodeship $voivodeships): FetchSchoolsJob => new FetchSchoolsJob($voivodeships, $schoolTypes));
        $batch = Bus::batch($jobs)->finally(fn(): bool => Cache::delete("fetch_schools"))->dispatch();
        Cache::set("fetch_schools", $batch->id);
        Cache::forget("fetched_schools");

        return response()->json(["message" => "Pobieranie rozpoczęte"], Status::HTTP_OK);
    }

    public function status(): JsonResponse
    {
        return response()->json([
            "done" => !$this->isFetching(),
            "count" => (int)Cache::get("fetched_schools"),
        ]);
    }

    protected function isFetching(): bool
    {
        $batch = $this->findBatch();

        return $batch !== null && !$batch->finished();
    }

    protected function findBatch(): ?Batch
    {
        $batchId = Cache::get("fetch_schools");

        if ($batchId === null) {
            return null;
        }

        return Bus::findBatch($batchId);
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

    private function filterDisabledSchools(Builder $query, Request $request): Builder
    {
        $showDisabled = $request->query("disabled", "false") === "true";

        if (!$showDisabled) {
            return $query->where("is_disabled", false);
        }

        return $query;
    }
}
