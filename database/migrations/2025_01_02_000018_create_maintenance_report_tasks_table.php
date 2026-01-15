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
        Schema::create('maintenance_report_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('maintenance_report_id')->constrained('maintenance_reports')->onDelete('cascade');
            $table->string('task_name');
            $table->text('task_description')->nullable();
            $table->text('comments')->nullable();
            $table->json('screenshots')->nullable();
            $table->integer('time_spent_minutes')->default(0);
            $table->integer('estimated_time_minutes')->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_report_tasks');
    }
};
