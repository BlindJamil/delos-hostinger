<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('name_ku')->nullable()->after('name_it');
            $table->text('description_ku')->nullable()->after('description_it');
            $table->json('features_ku')->nullable()->after('features_it');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['name_ku', 'description_ku', 'features_ku']);
        });
    }
};
