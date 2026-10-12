<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('answer_types', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('code', 100)->unique();
            $table->boolean('has_options')->default(false);
            $table->boolean('allows_multiple')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('answer_types');
    }
};
