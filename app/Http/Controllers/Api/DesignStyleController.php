<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DesignStyleResource;
use App\Services\DesignStyleService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DesignStyleController extends Controller
{
    public function __construct(private readonly DesignStyleService $designStyleService) {}

    public function index(): AnonymousResourceCollection
    {
        return DesignStyleResource::collection($this->designStyleService->getAllDesignStyles());
    }
}
