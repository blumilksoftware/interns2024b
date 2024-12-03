<?php

declare(strict_types=1);

namespace App\Http\Integrations\RSPOConnector;

use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\PaginationPlugin\Contracts\HasPagination;
use Saloon\PaginationPlugin\Paginator;
use Saloon\Traits\Plugins\AcceptsJson;

class RSPOConnector extends Connector implements HasPagination
{
    use AcceptsJson;

    public function resolveBaseUrl(): string
    {
        return "https://api-rspo.men.gov.pl/api";
    }

    public function paginate(Request $request): Paginator
    {
        return new RSPOPaginator($this, $request);
    }

    protected function defaultHeaders(): array
    {
        return [
            "Accept" => "application/ld+json",
        ];
    }
}
