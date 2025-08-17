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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('about_title')->nullable();
            $table->text('about_content')->nullable();

            $table->string('privacy_policy_title')->nullable();
            $table->text('privacy_policy_content')->nullable();

            $table->string('terms_of_service_title')->nullable();
            $table->text('terms_of_service_content')->nullable();

            $table->string('faq_title')->nullable();
            $table->text('faq_content')->nullable();
            $table->json('faq')->nullable();

            $table->json('contact')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
