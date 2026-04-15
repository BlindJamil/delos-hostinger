<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_ar')->nullable();
            $table->string('name_it')->nullable();
            $table->string('role_en');
            $table->string('role_ar')->nullable();
            $table->string('role_it')->nullable();
            $table->string('branch')->nullable();
            $table->text('achievement_en')->nullable();
            $table->text('achievement_ar')->nullable();
            $table->text('achievement_it')->nullable();
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0)->index();
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
