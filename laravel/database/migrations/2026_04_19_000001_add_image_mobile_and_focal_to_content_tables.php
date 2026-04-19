<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Hybrid responsive images — schema additions for five content tables.
 *
 * Every model-backed image now carries two optional tunings:
 *   image_mobile  — alternate image served on phones (≤ 767px)
 *   focal_point   — CSS object-position string like "30% 60%"
 *
 * Both columns are nullable so every existing row continues to work with
 * zero data migration; the <x-responsive-image> component renders
 * byte-identical HTML when both are null. Rollback drops the same columns.
 */
return new class extends Migration {
    private array $tables = ['brands', 'services', 'projects', 'employees', 'branches'];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                // Alternate image for the (max-width: 767px) media query.
                $t->string('image_mobile')->nullable()->after('image');
                // CSS object-position format, e.g. "30% 60%". 15 chars is
                // generous: max legal value is "100% 100%" (9 chars).
                $t->string('focal_point', 15)->nullable()->after('image_mobile');
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropColumn(['image_mobile', 'focal_point']);
            });
        }
    }
};
