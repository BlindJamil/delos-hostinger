<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('name_ku')->nullable()->after('name_it');
            $table->string('role_ku')->nullable()->after('role_it');
            $table->text('achievement_ku')->nullable()->after('achievement_it');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['name_ku', 'role_ku', 'achievement_ku']);
        });
    }
};
