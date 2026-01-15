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
        Schema::create('maintenance_task_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('report_type_id')->constrained('maintenance_report_types')->onDelete('cascade');
            $table->string('task_name'); // Task title
            $table->text('task_description')->nullable(); // Detailed description
            $table->integer('estimated_time_minutes')->default(30); // Time in minutes
            $table->integer('display_order')->default(0); // Sort order
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_task_templates');
    }
};
