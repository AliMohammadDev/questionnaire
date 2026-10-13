<?php

use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\ProjectCategoryController;
use Illuminate\Support\Facades\Route;

Route::get('departments', [DepartmentController::class, 'index'])->name('api.departments.index');
Route::get('project-categories', [ProjectCategoryController::class, 'index'])->name('api.project-categories.index');
