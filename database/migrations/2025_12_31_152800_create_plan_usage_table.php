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
        Schema::create('plan_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('month', 7); // Format: YYYY-MM
            
            // Current usage counts
            $table->integer('users_count')->default(0);
            $table->integer('projects_count')->default(0);
            $table->integer('clients_count')->default(0);
            $table->integer('tracked_hours')->default(0); // Minutes tracked this month
            $table->integer('invoices_count')->default(0);
            
            // Snapshot taken at
            $table->timestamp('snapshot_at')->nullable();
            
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
        Schema::dropIfExists('plan_usage');
    }
};
