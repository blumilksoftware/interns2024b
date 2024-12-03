<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\SchoolType;
use App\Enums\Voivodeship;
use App\Services\GetSchoolDataService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;

class FetchSchoolsJob implements ShouldQueue
{
    use Queueable;
    use Batchable;

    /**
     * @param array<SchoolType> $schoolTypes
     */
    public function __construct(
        protected Voivodeship $voivodeship,
        protected array $schoolTypes,
    ) {}

    public function handle(GetSchoolDataService $service): void
    {
        $added_schools = $service->getSchools($this->voivodeship, $this->schoolTypes);
        Cache::set("fetched_schools", $added_schools);
    }
}
