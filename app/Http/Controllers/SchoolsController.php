<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\VoivodeshipsHelper;
use App\Jobs\FetchSchoolsJob;
use App\Models\School;
use Illuminate\Bus\Batch;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SchoolsController extends Controller
{
    public function index(): JsonResponse
    {
        $data = School::all();

        return response()->json($data);
    }

    /**
     * @throws Throwable
     */
    public function fetch(): JsonResponse
    {
        if ($this->isFetching()) {
            return response()->json(["message" => "please wait"], Response::HTTP_CONFLICT);
        }

        $jobs = [new FetchSchoolsJob(VoivodeshipsHelper::LOWER_SILESIA)];
        $batch = Bus::batch($jobs)->finally(fn(): bool => Cache::delete("fetch_schools"))->dispatch();
        Cache::set("fetch_schools", $batch->id);

        return response()->json(["message" => "fetching started"], Response::HTTP_OK);
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
