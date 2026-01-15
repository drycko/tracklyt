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
        Schema::create('twilio_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('month', 7); // Format: YYYY-MM
            
            $table->integer('whatsapp_count')->default(0);
            $table->integer('sms_count')->default(0);
            $table->integer('total_messages')->default(0);
            
            $table->decimal('whatsapp_cost', 10, 4)->default(0); // Track costs if needed
            $table->decimal('sms_cost', 10, 4)->default(0);
            $table->decimal('total_cost', 10, 4)->default(0);
            
            $table->timestamps();
            
            $table->unique(['tenant_id', 'month']);
            $table->index('month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('twilio_usage');
    }
};
