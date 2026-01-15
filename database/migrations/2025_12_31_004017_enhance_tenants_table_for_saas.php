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
        Schema::table('tenants', function (Blueprint $table) {
            // Company Information
            $table->string('company_name')->nullable()->after('name');
            $table->string('company_logo')->nullable()->after('company_name');
            $table->string('website')->nullable()->after('company_logo');
            $table->string('phone')->nullable()->after('website');
            
            // Address Information
            $table->text('address')->nullable()->after('phone');
            $table->string('city')->nullable()->after('address');
            $table->string('state')->nullable()->after('city');
            $table->string('postal_code')->nullable()->after('state');
            $table->string('country')->default('South Africa')->after('postal_code');
            
            // Business Information
            $table->string('currency', 3)->default('ZAR')->after('country');
            $table->string('timezone')->default('Africa/Johannesburg')->after('currency');
            $table->enum('company_size', ['1-5', '6-10', '11-25', '26-50', '51-100', '100+'])->nullable()->after('timezone');
            $table->string('industry')->nullable()->after('company_size');
            $table->string('tax_number')->nullable()->after('industry');
            
            // Subscription & Billing
            $table->string('subscription_status')->default('trialing')->after('plan'); // trialing, active, past_due, canceled, paused
            $table->timestamp('trial_ends_at')->nullable()->after('subscription_status');
            $table->timestamp('subscription_ends_at')->nullable()->after('trial_ends_at');
            $table->decimal('monthly_amount', 10, 2)->default(0)->after('subscription_ends_at');
            $table->string('payment_method')->nullable()->after('monthly_amount'); // card, eft, manual
            $table->string('stripe_customer_id')->nullable()->after('payment_method');
            $table->string('stripe_subscription_id')->nullable()->after('stripe_customer_id');
            
            // Plan Limits
            $table->integer('max_users')->default(1)->after('stripe_subscription_id');
            $table->integer('max_projects')->default(5)->after('max_users');
            $table->integer('max_clients')->default(10)->after('max_projects');
            $table->integer('max_storage_mb')->default(1000)->after('max_clients'); // 1GB default
            
            // Onboarding & Configuration
            $table->boolean('is_onboarded')->default(false)->after('max_storage_mb');
            $table->integer('onboarding_step')->default(0)->after('is_onboarded'); // 0-5 for multi-step process
            $table->timestamp('onboarded_at')->nullable()->after('onboarding_step');
            
            // Settings & Metadata
            $table->json('settings')->nullable()->after('onboarded_at');
            $table->json('metadata')->nullable()->after('settings');
            
            // Billing Contact (can be different from owner)
            $table->string('billing_contact_name')->nullable()->after('billing_email');
            $table->string('billing_contact_phone')->nullable()->after('billing_contact_name');
            
            // Features & Flags
            $table->json('enabled_features')->nullable()->after('billing_contact_phone');
            $table->boolean('allow_client_portal')->default(false)->after('enabled_features');
            $table->boolean('allow_api_access')->default(false)->after('allow_client_portal');
            
            // Usage Tracking
            $table->integer('current_users_count')->default(0)->after('allow_api_access');
            $table->integer('current_projects_count')->default(0)->after('current_users_count');
            $table->integer('current_clients_count')->default(0)->after('current_projects_count');
            $table->bigInteger('current_storage_mb')->default(0)->after('current_clients_count');
            
            // Notes & Admin
            $table->text('notes')->nullable()->after('current_storage_mb');
            $table->timestamp('last_activity_at')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'company_name',
                'company_logo',
                'website',
                'phone',
                'address',
                'city',
                'state',
                'postal_code',
                'country',
                'currency',
                'timezone',
                'company_size',
                'industry',
                'tax_number',
                'subscription_status',
                'trial_ends_at',
                'subscription_ends_at',
                'monthly_amount',
                'payment_method',
                'stripe_customer_id',
                'stripe_subscription_id',
                'max_users',
                'max_projects',
                'max_clients',
                'max_storage_mb',
                'is_onboarded',
                'onboarding_step',
                'onboarded_at',
                'settings',
                'metadata',
                'billing_contact_name',
                'billing_contact_phone',
                'enabled_features',
                'allow_client_portal',
                'allow_api_access',
                'current_users_count',
                'current_projects_count',
                'current_clients_count',
                'current_storage_mb',
                'notes',
                'last_activity_at',
            ]);
        });
    }
};
