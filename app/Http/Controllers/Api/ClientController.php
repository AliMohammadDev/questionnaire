<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientRequest;
use App\Http\Resources\ClientResource;
use App\Services\ClientService;

class ClientController extends Controller
{
  public function __construct(private readonly ClientService $clientService) {}

  public function store(StoreClientRequest $request): ClientResource
  {
    return new ClientResource($this->clientService->storeClient($request->validated()));
  }
}
