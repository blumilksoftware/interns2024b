<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\GetSchoolDataService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchSchoolsJob implements ShouldQueue
{
    use Queueable;
    use Batchable;

    public function __construct(
        protected string $voivodeship,
    ) {}

    public function handle(GetSchoolDataService $service): void
    {
        $service->getSchools($this->voivodeship);
    }
}
