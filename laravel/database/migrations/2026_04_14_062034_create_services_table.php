<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('num', 10)->nullable();
            $table->string('slug')->unique();
            $table->string('name_en');
            $table->string('name_ar')->nullable();
            $table->string('name_it')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->text('description_it')->nullable();
            $table->json('features_en')->nullable();
            $table->json('features_ar')->nullable();
            $table->json('features_it')->nullable();
            $table->string('brand')->nullable();
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0)->index();
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
