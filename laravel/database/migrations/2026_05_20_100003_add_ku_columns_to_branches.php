<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->string('name_ku')->nullable()->after('name_it');
            $table->string('address_ku')->nullable()->after('address_it');
            $table->string('hours_ku')->nullable()->after('hours_it');
            $table->string('established_ku')->nullable()->after('established_it');
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['name_ku', 'address_ku', 'hours_ku', 'established_ku']);
        });
    }
};
