<?php

use App\Http\Controllers\Api\AnswerTypeController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\DesignStyleController;
use App\Http\Controllers\Api\ProjectCategoryController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ProjectQuestionnaireController;
use App\Http\Controllers\Api\ProjectTypeController;
use Illuminate\Support\Facades\Route;

Route::get('departments', [DepartmentController::class, 'index'])->name('api.departments.index');
Route::get('answer-types', [AnswerTypeController::class, 'index'])->name('api.answer-types.index');
Route::get('project-categories', [ProjectCategoryController::class, 'index'])->name('api.project-categories.index');
Route::get('project-types', [ProjectTypeController::class, 'index'])->name('api.project-types.index');
Route::get('design-styles', [DesignStyleController::class, 'index'])->name('api.design-styles.index');
Route::post('clients', [ClientController::class, 'store'])->name('api.clients.store');
Route::post('projects', [ProjectController::class, 'store'])->name('api.projects.store');
Route::get('projects/{project}/questionnaire', [ProjectQuestionnaireController::class, 'index'])->name('api.projects.questionnaire.index');
