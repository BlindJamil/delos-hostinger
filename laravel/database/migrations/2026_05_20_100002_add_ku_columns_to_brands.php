<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->string('category_ku')->nullable()->after('category_it');
            $table->string('origin_ku')->nullable()->after('origin_it');
            $table->text('description_ku')->nullable()->after('description_it');
            $table->json('specialties_ku')->nullable()->after('specialties_it');
        });
    }

    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn(['category_ku', 'origin_ku', 'description_ku', 'specialties_ku']);
        });
    }
};
