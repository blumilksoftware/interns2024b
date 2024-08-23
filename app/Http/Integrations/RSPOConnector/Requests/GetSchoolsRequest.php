<?php

declare(strict_types=1);

namespace App\Http\Integrations\RSPOConnector\Requests;

use App\DTO\SchoolDTO;
use JsonException;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

class GetSchoolsRequest extends Request implements Paginatable
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $voivodeship,
        protected int $schoolType,
    ) {}

    public function defaultQuery(): array
    {
        return [
            "zlikwidowana" => false,
            "typ_podmiotu_id" => $this->school_type,
            "wojewodztwo_nazwa" => $this->voivodeship,
        ];
    }

    /**
     * @throws JsonException
     */
    public function createDtoFromResponse(Response $response): array
    {
        $schools = [];

        if ($response->json() !== null) {
            foreach ($response->json("hydra:member") as $data) {
                $schools[] = SchoolDTO::createFromArray($data);
            }
        }

        return $schools;
    }

    public function resolveEndpoint(): string
    {
        return "/placowki";
    }
}
