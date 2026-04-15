<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category_en')->nullable();
            $table->string('category_ar')->nullable();
            $table->string('category_it')->nullable();
            $table->string('origin_en')->nullable();
            $table->string('origin_ar')->nullable();
            $table->string('origin_it')->nullable();
            $table->string('since')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->text('description_it')->nullable();
            $table->json('specialties_en')->nullable();
            $table->json('specialties_ar')->nullable();
            $table->json('specialties_it')->nullable();
            $table->string('image')->nullable();
            $table->string('url')->nullable();
            $table->integer('sort_order')->default(0)->index();
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
