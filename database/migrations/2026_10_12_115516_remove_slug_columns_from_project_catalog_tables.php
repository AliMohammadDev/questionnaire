<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'departments' => ['slug'],
            'project_categories' => ['department_id', 'slug'],
            'project_types' => ['project_category_id', 'slug'],
            'design_styles' => ['slug'],
        ] as $tableName => $columns) {
            if (! Schema::hasColumn($tableName, 'slug')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($columns): void {
                $table->dropUnique($columns);
                $table->dropColumn('slug');
            });
        }
    }

    /**
     * Restore nullable columns; the removed slug values cannot be recovered.
     */
    public function down(): void
    {
        foreach ([
            'departments' => ['slug'],
            'project_categories' => ['department_id', 'slug'],
            'project_types' => ['project_category_id', 'slug'],
            'design_styles' => ['slug'],
        ] as $tableName => $columns) {
            Schema::table($tableName, function (Blueprint $table) use ($columns): void {
                $table->string('slug')->nullable();
                $table->unique($columns);
            });
        }
    }
};
