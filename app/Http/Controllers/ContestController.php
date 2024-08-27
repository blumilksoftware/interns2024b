<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\SchoolResource;
use App\Models\School;
use Inertia\Inertia;
use Inertia\Response;

class ContestController extends Controller
{
    public function index(): Response
    {
        $schools = School::all()->sortBy("name");

        return Inertia::render("Home", ["schools" => SchoolResource::collection($schools)]);
    }

    public function create(): Response
    {
        return Inertia::render("Dashboard");
    }
}
