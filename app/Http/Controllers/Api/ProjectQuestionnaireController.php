<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionnaireQuestionResource;
use App\Models\Project;
use App\Services\QuestionnaireService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectQuestionnaireController extends Controller
{
    public function __construct(private readonly QuestionnaireService $questionnaireService) {}

    public function index(Project $project): AnonymousResourceCollection
    {
        return QuestionnaireQuestionResource::collection(
            $this->questionnaireService->getQuestions($project->getAttributes()),
        )->additional(['project_id' => $project->id, 'additional_questions' => $project->additional_questions ?? []]);
    }
}
