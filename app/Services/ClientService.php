<?php

namespace App\Services;

use App\Models\Client;

class ClientService
{
  /** @param array{name: string, phone: string, email?: string|null, address?: string|null} $data */
  public function storeClient(array $data): Client
  {
    return Client::create($data);
  }
}
