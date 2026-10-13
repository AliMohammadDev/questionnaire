<?php

namespace App\Http\Resources;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Client */
class ClientResource extends JsonResource
{
  /** @return array{id: int, name: string, phone: string, email: string|null, address: string|null} */
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'phone' => $this->phone,
      'email' => $this->email,
      'address' => $this->address,
    ];
  }
}
