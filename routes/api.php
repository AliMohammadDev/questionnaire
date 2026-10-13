<?php

use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\DesignStyleController;
use App\Http\Controllers\Api\ProjectCategoryController;
use App\Http\Controllers\Api\ProjectTypeController;
use Illuminate\Support\Facades\Route;

Route::get('departments', [DepartmentController::class, 'index'])->name('api.departments.index');
Route::get('project-categories', [ProjectCategoryController::class, 'index'])->name('api.project-categories.index');
Route::get('project-types', [ProjectTypeController::class, 'index'])->name('api.project-types.index');
Route::get('design-styles', [DesignStyleController::class, 'index'])->name('api.design-styles.index');
