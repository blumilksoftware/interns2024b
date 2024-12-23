<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\SchoolDTO;
use App\Enums\SchoolType;
use App\Enums\Voivodeship;
use App\Http\Integrations\RSPOConnector\Requests\GetSchoolsRequest;
use App\Http\Integrations\RSPOConnector\RSPOConnector;
use App\Models\School;
use Illuminate\Support\Collection;

class GetSchoolDataService
{
    public function __construct(
        protected RSPOConnector $connector,
    ) {}

    /**
     * @param array<SchoolType> $schoolTypes
     */
    public function getSchools(Voivodeship $voivodeship, array $schoolTypes): int
    {
        return $this->store($this->fetchSchools($voivodeship, $schoolTypes));
    }

    /**
     * @param array<SchoolType> $schoolTypes
     * @return Collection<SchoolDTO> $schools
     */
    protected function fetchSchools(Voivodeship $voivodeship, array $schoolTypes): Collection
    {
        $schools = collect();

        foreach ($schoolTypes as $type) {
            $request = new GetSchoolsRequest($voivodeship, $type);
            $schools->push(...$this->connector->paginate($request)->collect()->collect());
        }

        return $schools;
    }

    /**
     * @param Collection<SchoolDTO> $schools
     */
    protected function store(Collection $schools): int
    {
        $fetched = 0;

        foreach ($schools as $dto) {
            if (School::query()->where("regon", "=", $dto->regon)->doesntExist()) {
                School::query()->create($dto->toArray());
                ++$fetched;
            }
        }

        return $fetched;
    }
}
