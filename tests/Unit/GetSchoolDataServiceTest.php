<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\SchoolType;
use App\Enums\Voivodeship;
use App\Http\Integrations\RSPOConnector\Requests\GetSchoolsRequest;
use App\Http\Integrations\RSPOConnector\RSPOConnector;
use App\Services\GetSchoolDataService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Saloon\Config;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Tests\TestCase;

class GetSchoolDataServiceTest extends TestCase
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
                        "regon" => "356855674",
                    ],
                    [
                        "id" => 2,
                        "nazwa" => "test school 2",
                        "miejscowosc" => "test city",
                        "ulica" => "example",
                        "numerBudynku" => "20",
                        "numerLokalu" => "2c",
                        "kodPocztowy" => "00-000",
                        "regon" => "356855675",
                    ],
                ],
                "hydra:view" => [],
            ], 200),
        ]);

        $this->connector->withMockClient($mockClient);
        $this->service->getSchools(Voivodeship::LOWER_SILESIA, [SchoolType::TECHNIKUM]);

        $this->assertDatabaseHas("schools", [
            "name" => "test school 1",
            "city" => "test city",
            "street" => "example",
            "building_number" => "13",
            "apartment_number" => "1c",
            "zip_code" => "00-000",
            "regon" => "356855674",
        ]);

        $this->assertDatabaseHas("schools", [
            "name" => "test school 2",
            "city" => "test city",
            "street" => "example",
            "building_number" => "20",
            "apartment_number" => "2c",
            "zip_code" => "00-000",
            "regon" => "356855675",
        ]);
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
                        "regon" => "356855674",
                    ],
                    [
                        "id" => 2,
                        "nazwa" => "test school 2",
                        "miejscowosc" => "test city",
                        "ulica" => "example",
                        "numerBudynku" => "20",
                        "numerLokalu" => "2c",
                        "kodPocztowy" => "00-000",
                        "regon" => "356855675",
                    ],
                ],
                "hydra:view" => [],
            ], 200),
        ]);

        $this->connector->withMockClient($mockClient);
        $result1 = $this->service->getSchools(Voivodeship::LOWER_SILESIA, [SchoolType::TECHNIKUM]);
        $result2 = $this->service->getSchools(Voivodeship::LOWER_SILESIA, [SchoolType::TECHNIKUM]);

        $this->assertDatabaseCount("schools", 2);
        $this->assertSame($result1, 2);
        $this->assertSame($result2, 0);
    }
}
