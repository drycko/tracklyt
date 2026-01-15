<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // tenant management
            'view tenants',
            'create tenants',
            'edit tenants',
            'delete tenants',

            // Tenant permissions
            'manage tenant settings',
            'manage tenant subscriptions',
            'manage tenant billing',

            // Client permissions
            'view clients',
            'create clients',
            'edit clients',
            'delete clients',
            
            // Quote permissions
            'view quotes',
            'create quotes',
            'edit quotes',
            'delete quotes',
            
            // Project permissions
            'view projects',
            'create projects',
            'edit projects',
            'delete projects',
            
            // Task permissions
            'view tasks',
            'create tasks',
            'edit tasks',
            'delete tasks',
            
            // Time entry permissions
            'view time-entries',
            'create time-entries',
            'edit time-entries',
            'delete time-entries',
            'view all time-entries',
            
            // Invoice permissions
            'view invoices',
            'create invoices',
            'edit invoices',
            'delete invoices',
            
            // Maintenance permissions
            'view maintenance-profiles',
            'create maintenance-profiles',
            'edit maintenance-profiles',
            'delete maintenance-profiles',
            
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Reports
            'view reports',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $superAdminRole = Role::create(['name' => 'super_admin']); // Platform super admin
        $superAdminRole->givePermissionTo(Permission::all()); // Super admin has all permissions

        // create platform support team role
        $supportRole = Role::create(['name' => 'support_agent']);
        $supportRole->givePermissionTo([
            'view tenants',
            'manage tenant billing',
            'view users',
        ]);
        
        $ownerRole = Role::create(['name' => 'owner']);
        $ownerRole->givePermissionTo(Permission::all()); // Owner has all permissions

        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo([
            'view clients', 'create clients', 'edit clients', 'delete clients',
            'view quotes', 'create quotes', 'edit quotes', 'delete quotes',
            'view projects', 'create projects', 'edit projects', 'delete projects',
            'view tasks', 'create tasks', 'edit tasks', 'delete tasks',
            'view time-entries', 'create time-entries', 'edit time-entries', 'delete time-entries', 'view all time-entries',
            'view invoices', 'create invoices', 'edit invoices', 'delete invoices',
            'view maintenance-profiles', 'create maintenance-profiles', 'edit maintenance-profiles', 'delete maintenance-profiles',
            'view users', 'create users', 'edit users',
            'view reports',
        ]);

        $staffRole = Role::create(['name' => 'staff']);
        $staffRole->givePermissionTo([
            'view clients',
            'view quotes',
            'view projects',
            'view tasks', 'create tasks', 'edit tasks',
            'view time-entries', 'create time-entries', 'edit time-entries',
            'view invoices',
            'view maintenance-profiles',
        ]);

        $clientRole = Role::create(['name' => 'client']);
        $clientRole->givePermissionTo([
            'view projects',
            'view invoices',
        ]);

        // Create platform super admin (not tied to any tenant)
        $superAdmin = User::create([
            'tenant_id' => null,
            'name' => 'Platform Admin',
            'email' => 'admin@tracklyt.com',
            'password' => Hash::make('password'),
            'is_super_admin' => true,
            'role' => 'super_admin',
            'is_active' => true,
        ]);
        $superAdmin->assignRole('super_admin');

        // create platform support users
        $supportUser = User::create([
            'tenant_id' => null,
            'name' => 'Support User',
            'email' => 'support@tracklyt.com',
            'password' => Hash::make('password'),
            'is_super_admin' => false,
            'role' => 'support_agent',
            'is_active' => true,
        ]);
        $supportUser->assignRole('support_agent');

        // Create demo tenant
        $tenant = Tenant::create([
            'name' => 'Demo Agency',
            'slug' => 'demo',
            'plan' => 'agency',
            'status' => 'active',
            'billing_email' => 'billing@demoagency.com',
        ]);

        // Create owner user
        $owner = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'John Owner',
            'email' => 'owner@demo.com',
            'password' => Hash::make('password'),
            'is_super_admin' => false,
            'role' => 'owner',
            'hourly_rate' => 150.00,
            'is_active' => true,
        ]);
        $owner->assignRole('owner');

        // Create admin user
        $admin = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Jane Admin',
            'email' => 'admin@demo.com',
            'password' => Hash::make('password'),
            'is_super_admin' => false,
            'role' => 'admin',
            'hourly_rate' => 100.00,
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        // Create staff user
        $staff = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Bob Staff',
            'email' => 'staff@demo.com',
            'password' => Hash::make('password'),
            'is_super_admin' => false,
            'role' => 'staff',
            'hourly_rate' => 75.00,
            'is_active' => true,
        ]);
        $staff->assignRole('staff');

        // Create client user
        $client = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Alice Client',
            'email' => 'client@demo.com',
            'password' => Hash::make('password'),
            'is_super_admin' => false,
            'role' => 'client',
            'hourly_rate' => null,
            'is_active' => true,
        ]);
        $client->assignRole('client');

        // seed maintenance profiles, projects, tasks, etc. as needed for demo tenant
        $this->call([
            SubscriptionPlanSeeder::class,
            MaintenanceReportTypeSeeder::class,

            // You can add more seeders here as needed
        ]);
    }
}
