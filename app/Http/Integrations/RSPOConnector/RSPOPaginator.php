<?php

declare(strict_types=1);

namespace App\Http\Integrations\RSPOConnector;

use JsonException;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\PagedPaginator;

class RSPOPaginator extends PagedPaginator
{
    /**
     * @throws JsonException
     */
    protected function isLastPage(Response $response): bool
    {
        return !key_exists("hydra:next", $response->json("hydra:view"));
    }

    /**
     * @throws JsonException
     */
    protected function getPageItems(Response $response, Request $request): array
    {
        return $response->dto();
    }

    protected function applyPagination(Request $request): Request
    {
        $request->query()->add("page", $this->currentPage + 1);

        return $request;
    }
}
