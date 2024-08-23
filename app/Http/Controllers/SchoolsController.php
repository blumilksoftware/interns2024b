<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SchoolRequest;
use App\Http\Resources\SchoolResource;
use App\Jobs\FetchSchoolsJob;
use App\Models\School;
use Illuminate\Bus\Batch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as Status;
use Throwable;

class SchoolsController extends Controller
{
    public function index(): Response
    {
        return Inertia::render("SchoolsPanel", ["schools" => SchoolResource::collection(School::all())]);
    }

    public function store(SchoolRequest $request): RedirectResponse
    {
        School::query()->create($request->validated());

        return redirect()
            ->back();
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
            ->back();
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
        $jobs = $voivodeships->map(fn(string $voivodeships): FetchSchoolsJob => new FetchSchoolsJob($voivodeships));
        $batch = Bus::batch($jobs)->finally(fn(): bool => Cache::delete("fetch_schools"))->dispatch();
        Cache::set("fetch_schools", $batch->id);

        return response()->json(["message" => "Pobieranie rozpoczęte"], Status::HTTP_OK);
    }

    public function status(): JsonResponse
    {
        return response()->json([
            "done" => !$this->isFetching(),
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
}
