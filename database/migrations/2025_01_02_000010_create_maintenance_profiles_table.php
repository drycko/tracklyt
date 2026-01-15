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
        Schema::create('maintenance_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->enum('maintenance_type', ['retainer', 'hourly'])->default('retainer');
            $table->decimal('monthly_hours', 10, 2)->nullable();
            $table->decimal('rate', 10, 2);
            $table->text('sla_notes')->nullable();
            $table->decimal('rollover_hours', 10, 2)->default(0);
            $table->date('start_date');
            $table->date('last_reset_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_profiles');
    }
};
