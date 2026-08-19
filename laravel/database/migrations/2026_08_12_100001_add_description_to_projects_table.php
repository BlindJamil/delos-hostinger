<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Rich-text project description, one column per supported locale — mirrors
 * the employees.achievement_* shape so Project::localized('description')
 * resolves with the same _en fallback. TEXT (not string) because the admin's
 * WYSIWYG editor emits HTML that can run long.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->text('description_en')->nullable()->after('type_label_ku');
            $table->text('description_ar')->nullable()->after('description_en');
            $table->text('description_it')->nullable()->after('description_ar');
            $table->text('description_ku')->nullable()->after('description_it');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['description_en', 'description_ar', 'description_it', 'description_ku']);
        });
    }
};
