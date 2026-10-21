<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_style_project', function (Blueprint $table): void {
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('design_style_id')->index()->constrained()->restrictOnDelete();
            $table->primary(['project_id', 'design_style_id']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('design_style_project');
    }
};
