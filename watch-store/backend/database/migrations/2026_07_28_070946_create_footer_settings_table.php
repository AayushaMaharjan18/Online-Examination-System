<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('footer_settings', function (Blueprint $table) {
            $table->id();
            
            // Brand section
            $table->string('brand_name')->default('WATCHSTORE');
            $table->text('brand_description')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('twitter_url')->nullable();
            
            // Quick Links
            $table->json('quick_links')->nullable()->comment('Array of {label, url}');
            
            // Customer Service Links
            $table->json('customer_service_links')->nullable()->comment('Array of {label, url}');
            
            // Contact Info
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            
            // Copyright
            $table->string('copyright_text')->nullable();
            
            // Payment methods
            $table->json('payment_methods')->nullable()->comment('Array of payment method names');
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('footer_settings');
    }
};