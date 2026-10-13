<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnswerTypeResource;
use App\Services\AnswerTypeService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AnswerTypeController extends Controller
{
  public function __construct(private readonly AnswerTypeService $answerTypeService) {}

  public function index(): AnonymousResourceCollection
  {
    return AnswerTypeResource::collection($this->answerTypeService->getAllAnswerTypes());
  }
}
