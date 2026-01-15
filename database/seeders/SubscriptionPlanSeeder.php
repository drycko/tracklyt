<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Trial',
                'slug' => 'trial',
                'description' => 'Try Tracklyt free for 14 days. Perfect for testing the platform.',
                'price_monthly' => 0.00,
                'price_yearly' => 0.00,
                'billing_cycle' => 'monthly',
                'max_users' => 1,
                'max_projects' => 2,
                'max_clients' => 5,
                'max_monthly_hours' => 40,
                'max_invoices_per_month' => 5,
                'max_twilio_messages_per_month' => 10,
                'has_time_tracking' => true,
                'has_invoicing' => true,
                'has_client_portal' => false,
                'has_maintenance_reports' => false,
                'has_advanced_reporting' => false,
                'has_api_access' => false,
                'has_white_label' => false,
                'has_priority_support' => false,
                'display_order' => 1,
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Freelancer',
                'slug' => 'freelancer',
                'description' => 'Perfect for solo developers and consultants managing multiple clients.',
                'price_monthly' => 99.00,
                'price_yearly' => 990.00, // ~17% discount
                'billing_cycle' => 'monthly',
                'max_users' => 1,
                'max_projects' => 10,
                'max_clients' => 25,
                'max_monthly_hours' => 200,
                'max_invoices_per_month' => 25,
                'max_twilio_messages_per_month' => 50,
                'has_time_tracking' => true,
                'has_invoicing' => true,
                'has_client_portal' => true,
                'has_maintenance_reports' => true,
                'has_advanced_reporting' => false,
                'has_api_access' => false,
                'has_white_label' => false,
                'has_priority_support' => false,
                'display_order' => 2,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Agency',
                'slug' => 'agency',
                'description' => 'Built for teams and agencies managing multiple projects and clients.',
                'price_monthly' => 199.00,
                'price_yearly' => 1990.00, // ~17% discount
                'billing_cycle' => 'monthly',
                'max_users' => 10,
                'max_projects' => 50,
                'max_clients' => 100,
                'max_monthly_hours' => 1000,
                'max_invoices_per_month' => 100,
                'max_twilio_messages_per_month' => 200,
                'has_time_tracking' => true,
                'has_invoicing' => true,
                'has_client_portal' => true,
                'has_maintenance_reports' => true,
                'has_advanced_reporting' => true,
                'has_api_access' => true,
                'has_white_label' => false,
                'has_priority_support' => true,
                'display_order' => 3,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Unlimited everything with white-label options and priority support.',
                'price_monthly' => 299.00,
                'price_yearly' => 2990.00, // ~17% discount
                'billing_cycle' => 'monthly',
                'max_users' => -1, // Unlimited
                'max_projects' => -1, // Unlimited
                'max_clients' => -1, // Unlimited
                'max_monthly_hours' => -1, // Unlimited
                'max_invoices_per_month' => -1, // Unlimited
                'max_twilio_messages_per_month' => -1, // Unlimited
                'has_time_tracking' => true,
                'has_invoicing' => true,
                'has_client_portal' => true,
                'has_maintenance_reports' => true,
                'has_advanced_reporting' => true,
                'has_api_access' => true,
                'has_white_label' => true,
                'has_priority_support' => true,
                'display_order' => 4,
                'is_active' => true,
                'is_featured' => false,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }

        $this->command->info('Subscription plans seeded successfully!');
    }
}
