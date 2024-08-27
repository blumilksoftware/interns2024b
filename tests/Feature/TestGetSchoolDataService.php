<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Helpers\VoivodeshipsHelper;
use App\Http\Integrations\RSPOConnector\Requests\GetSchoolsRequest;
use App\Http\Integrations\RSPOConnector\RSPOConnector;
use App\Services\GetSchoolDataService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Saloon\Config;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Tests\TestCase;

class TestGetSchoolDataService extends TestCase
{
    use RefreshDatabase;

    protected RSPOConnector $connector;
    protected GetSchoolDataService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Config::preventStrayRequests();

        $this->connector = new RSPOConnector();
        $this->service = new GetSchoolDataService($this->connector);

        MockClient::destroyGlobal();
    }

    public function testFetchSchools(): void
    {
        $mockClient = new MockClient([
            GetSchoolsRequest::class => MockResponse::make([
                "hydra:member" => [
                    [
                        "id" => 1,
                        "nazwa" => "test school 1",
                        "miejscowosc" => "test city",
                        "ulica" => "example",
                        "numerBudynku" => "13",
                        "numerLokalu" => "1c",
                        "kodPocztowy" => "00-000",
                    ],
                    [
                        "id" => 2,
                        "nazwa" => "test school 2",
                        "miejscowosc" => "test city",
                        "ulica" => "example",
                        "numerBudynku" => "20",
                        "numerLokalu" => "2c",
                        "kodPocztowy" => "00-000",
                    ],
                ],
                "hydra:view" => [],
            ], 200),
        ]);

        $this->connector->withMockClient($mockClient);
        $this->service->getSchools(VoivodeshipsHelper::LOWER_SILESIA);

        $this->assertDatabaseHas("schools", [
            "name" => "test school 1",
            "city" => "test city",
            "street" => "example",
            "building_number" => "13",
            "apartment_number" => "1c",
            "zip_code" => "00-000",
        ]);

        $this->assertDatabaseHas("schools", [
            "name" => "test school 2",
            "city" => "test city",
            "street" => "example",
            "building_number" => "20",
            "apartment_number" => "2c",
            "zip_code" => "00-000",
        ]);
    }

    public function testFetchSchoolsWithWrongVoivodeship(): void
    {
        $mockClient = new MockClient([
            GetSchoolsRequest::class => MockResponse::make([
                "hydra:member" => [],
                "hydra:view" => [],
            ], 200),
        ]);

        $this->connector->withMockClient($mockClient);
        $this->service->getSchools("wrong");

        $this->assertDatabaseCount("schools", 0);
    }

    public function testFetchSchoolsWithAlreadyFetchedData(): void
    {
        $mockClient = new MockClient([
            GetSchoolsRequest::class => MockResponse::make([
                "hydra:member" => [
                    [
                        "id" => 1,
                        "nazwa" => "test school 1",
                        "miejscowosc" => "test city",
                        "ulica" => "example",
                        "numerBudynku" => "13",
                        "numerLokalu" => "1c",
                        "kodPocztowy" => "00-000",
                    ],
                    [
                        "id" => 2,
                        "nazwa" => "test school 2",
                        "miejscowosc" => "test city",
                        "ulica" => "example",
                        "numerBudynku" => "20",
                        "numerLokalu" => "2c",
                        "kodPocztowy" => "00-000",
                    ],
                ],
                "hydra:view" => [],
            ], 200),
        ]);

        $this->connector->withMockClient($mockClient);
        $this->service->getSchools(VoivodeshipsHelper::LOWER_SILESIA);
        $this->service->getSchools(VoivodeshipsHelper::LOWER_SILESIA);

        $this->assertDatabaseCount("schools", 2);
    }
}
