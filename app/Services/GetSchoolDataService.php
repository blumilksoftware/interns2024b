<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\SchoolDTO;
use App\Helpers\SchoolTypeHelper;
use App\Http\Integrations\RSPOConnector\Requests\GetSchoolsRequest;
use App\Http\Integrations\RSPOConnector\RSPOConnector;
use App\Models\School;
use Illuminate\Support\Collection;

class GetSchoolDataService
{
    public function __construct(
        protected RSPOConnector $connector,
    ) {}

    public function getSchools(string $voivodeship): void
    {
        $this->store($this->fetchSchools($voivodeship));
    }

    /**
     * @return Collection<SchoolDTO> $schools
     */
    protected function fetchSchools(string $voivodeship): Collection
    {
        $schools = collect();

        foreach (SchoolTypeHelper::all() as $schoolType) {
            $request = new GetSchoolsRequest($voivodeship, $schoolType);
            $schools->push(...$this->connector->paginate($request)->collect()->collect());
        }

        return $schools;
    }

    /**
     * @param Collection<SchoolDTO> $schools
     */
    protected function store(Collection $schools): void
    {
        foreach ($schools as $school) {
            School::firstOrCreate([
                "name" => $school->name,
                "city" => $school->city,
                "street" => $school->street,
                "building_number" => $school->buildingNumber,
                "apartment_number" => $school->apartmentNumber,
                "zip_code" => $school->zipCode,
            ]);
        }
    }
}
