<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title_en');
            $table->string('title_ar')->nullable();
            $table->string('title_it')->nullable();
            $table->string('city')->nullable();
            $table->string('type')->nullable()->index();
            $table->string('type_label_en')->nullable();
            $table->string('type_label_ar')->nullable();
            $table->string('type_label_it')->nullable();
            $table->string('brand')->nullable();
            $table->year('year')->nullable();
            $table->string('image')->nullable();
            $table->boolean('featured')->default(false)->index();
            $table->integer('sort_order')->default(0)->index();
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
