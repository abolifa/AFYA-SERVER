<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('post_views', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 255)->index();
            $table->string('visitor_hash', 64)->nullable();
            $table->date('view_date')->index();
            $table->unsignedInteger('views')->default(1);
            $table->timestamps();
            $table->unique(['slug', 'visitor_hash', 'view_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_views');
    }
};
