<?php

use App\Models\Client;
use App\Models\Department;
use App\Models\ProjectCategory;
use App\Models\ProjectType;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(User::class)->nullable()->constrained();
            $table->foreignIdFor(Client::class, 'client_id')->index()->constrained();
            $table->foreignIdFor(Department::class, 'department_id')->index()->constrained();
            $table->foreignIdFor(ProjectCategory::class, 'project_category_id')->index()->constrained();
            $table->foreignIdFor(ProjectType::class, 'project_type_id')->index()->constrained();
            $table->string('project_code', 100)->unique();
            $table->string('project_name')->nullable();
            $table->string('status', 50)->default('draft');
            $table->text('notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->index('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
