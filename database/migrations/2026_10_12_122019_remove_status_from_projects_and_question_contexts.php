<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('projects', 'status')) {
            Schema::table('projects', function (Blueprint $table): void {
                $table->dropIndex(['status']);
                $table->dropColumn('status');
            });
        }
        if (Schema::hasColumn('question_contexts', 'status')) {
            Schema::table('question_contexts', function (Blueprint $table): void {
                $table->dropColumn('status');
            });
        }
    }

    /**
     * Restore nullable columns without recreating removed status values.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table): void {
            $table->string('status', 50)->nullable()->index();
        });
        Schema::table('question_contexts', function (Blueprint $table): void {
            $table->string('status')->nullable();
        });
    }
};
