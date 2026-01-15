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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Trial, Freelancer, Agency, Enterprise
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price_monthly', 10, 2)->default(0);
            $table->decimal('price_yearly', 10, 2)->default(0);
            $table->enum('billing_cycle', ['monthly', 'yearly', 'lifetime'])->default('monthly');
            
            // Feature Limits
            $table->integer('max_users')->default(1); // -1 for unlimited
            $table->integer('max_projects')->default(5); // -1 for unlimited
            $table->integer('max_clients')->default(10); // -1 for unlimited
            $table->integer('max_monthly_hours')->default(100); // Time tracking hours per month
            $table->integer('max_invoices_per_month')->default(10); // -1 for unlimited
            $table->integer('max_twilio_messages_per_month')->default(0); // WhatsApp/SMS messages
            
            // Features (boolean flags)
            $table->boolean('has_time_tracking')->default(true);
            $table->boolean('has_invoicing')->default(true);
            $table->boolean('has_client_portal')->default(false);
            $table->boolean('has_maintenance_reports')->default(false);
            $table->boolean('has_advanced_reporting')->default(false);
            $table->boolean('has_api_access')->default(false);
            $table->boolean('has_white_label')->default(false);
            $table->boolean('has_priority_support')->default(false);
            
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
