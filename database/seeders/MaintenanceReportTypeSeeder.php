<?php

namespace Database\Seeders;

use App\Models\MaintenanceReportType;
use App\Models\MaintenanceTaskTemplate;
use Illuminate\Database\Seeder;

class MaintenanceReportTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get tenant_id (adjust this based on your needs)
        $tenantId = 1;

        // Web Maintenance Report
        $webMaintenance = MaintenanceReportType::create([
            'tenant_id' => $tenantId,
            'name' => 'Web Maintenance Report',
            'description' => 'Standard website maintenance checklist',
            'footer_text' => 'WEBSITE MAINTENANCE REPORT',
            'is_active' => true,
        ]);

        $this->createMaintenanceTasks($webMaintenance->id, $tenantId, [
            ['task' => 'Notify team that maintenance is about to begin', 'time' => 1],
            ['task' => 'Backup website files & DB', 'time' => 2],
            ['task' => 'Update WordPress Core', 'time' => 2],
            ['task' => 'Update plugins and themes', 'time' => 2],
            ['task' => 'List of Inactive Plugins', 'time' => 2],
            ['task' => 'Monitor licensed plugins', 'time' => 2],
            ['task' => 'Monitor licensed themes', 'time' => 2],
            ['task' => 'Clear Cache', 'time' => 2],
            ['task' => 'Check permalinks', 'time' => 2],
            ['task' => 'Check SSL certification linking', 'time' => 2],
            ['task' => 'Check and test ALL lead and contact forms', 'time' => 2],
            ['task' => 'Check that ALL lead and contact forms redirect to a Thank You Page', 'time' => 2],
            ['task' => 'Check DB for ALL lead and contact forms', 'time' => 2],
            ['task' => 'Check test gmail account for ALL lead and contact forms', 'time' => 2],
            ['task' => 'Check that ALL maps load correctly', 'time' => 2],
            ['task' => 'Check and clear all notifications and spam comments', 'time' => 2],
            ['task' => 'Check security plugin and status of errors or issues', 'time' => 2],
            ['task' => 'Browse the site to ensure pages are loading and linking correctly', 'time' => 2],
            ['task' => 'Check that the GA code is still in place', 'time' => 2],
            ['task' => 'Conduct a speed and performance check', 'time' => 1],
            ['task' => 'Send report to team', 'time' => 1],
        ]);

        // Web Move Report
        $webMove = MaintenanceReportType::create([
            'tenant_id' => $tenantId,
            'name' => 'Web Move Report',
            'description' => 'Website migration and server transfer checklist',
            'footer_text' => 'WEBSITE MIGRATION REPORT',
            'is_active' => true,
        ]);

        $this->createMaintenanceTasks($webMove->id, $tenantId, [
            ['task' => 'Notify team listed that you are about to move the website', 'time' => 2],
            ['task' => 'Backup website files & Database', 'time' => 2],
            ['task' => 'OLD SERVER -> Update plugins and themes', 'time' => 2],
            ['task' => 'OLD SERVER -> Update WordPress Core', 'time' => 2],
            ['task' => 'OLD SERVER -> Manually update site to PHP 8 or 8.1 - check compatibility', 'time' => 2],
            ['task' => 'OLD SERVER -> Check permalinks', 'time' => 2],
            ['task' => 'OLD SERVER -> Check SSL certificate linking', 'time' => 2],
            ['task' => 'OLD SERVER -> Check and test all lead and contact forms', 'time' => 2],
            ['task' => 'SETUP the website on the new server', 'time' => 2],
            ['task' => 'NEW SERVER -> Check that all lead and contact forms redirects to a Thank You Page', 'time' => 2],
            ['task' => 'NEW SERVER -> Check that map loads correctly', 'time' => 2],
            ['task' => 'NEW SERVER -> Check DB for lead and contact forms', 'time' => 2],
            ['task' => 'NEW SERVER -> Check test gmail account for lead and contact form test', 'time' => 2],
            ['task' => 'NEW SERVER -> Check security plugin and status of errors or issues', 'time' => 2],
            ['task' => 'NEW SERVER -> Browse the site to ensure pages are loading and linking correctly', 'time' => 2],
            ['task' => 'NEW SERVER -> Check that GA code is still in place', 'time' => 2],
        ]);

        // Web Updates Report
        $webUpdates = MaintenanceReportType::create([
            'tenant_id' => $tenantId,
            'name' => 'Web Updates Report',
            'description' => 'Website content and feature updates',
            'footer_text' => 'WEBSITE UPDATES REPORT',
            'is_active' => true,
        ]);

        $this->createMaintenanceTasks($webUpdates->id, $tenantId, [
            ['task' => 'Notify team that update is about to start', 'time' => 1],
            ['task' => 'Backup website files & DB', 'time' => 2],
            ['task' => 'Implement requested updates', 'time' => 5],
            ['task' => 'Test updated functionality', 'time' => 3],
            ['task' => 'Clear cache and check performance', 'time' => 2],
            ['task' => 'Send report', 'time' => 1],
        ]);

        // Website Fix
        $websiteFix = MaintenanceReportType::create([
            'tenant_id' => $tenantId,
            'name' => 'Website Fix',
            'description' => 'Emergency fixes and bug resolution',
            'footer_text' => 'WEBSITE FIX REPORT',
            'is_active' => true,
        ]);

        $this->createMaintenanceTasks($websiteFix->id, $tenantId, [
            ['task' => 'Identify and document the issue', 'time' => 2],
            ['task' => 'Backup website files & DB', 'time' => 2],
            ['task' => 'Implement fix', 'time' => 5],
            ['task' => 'Test fix thoroughly', 'time' => 3],
            ['task' => 'Monitor for additional issues', 'time' => 2],
            ['task' => 'Document resolution', 'time' => 1],
        ]);

        // Web Backup Only
        $webBackup = MaintenanceReportType::create([
            'tenant_id' => $tenantId,
            'name' => 'Web Backup Only',
            'description' => 'Backup website files and database',
            'footer_text' => 'WEBSITE BACKUP REPORT',
            'is_active' => true,
        ]);

        $this->createMaintenanceTasks($webBackup->id, $tenantId, [
            ['task' => 'Backup website files', 'time' => 2],
            ['task' => 'Backup database', 'time' => 2],
            ['task' => 'Verify backup integrity', 'time' => 1],
            ['task' => 'Store backup in secure location', 'time' => 1],
        ]);

        // Security Audit
        $securityAudit = MaintenanceReportType::create([
            'tenant_id' => $tenantId,
            'name' => 'Security Audit',
            'description' => 'Comprehensive security review and hardening',
            'footer_text' => 'SECURITY AUDIT REPORT',
            'is_active' => true,
        ]);

        $this->createMaintenanceTasks($securityAudit->id, $tenantId, [
            ['task' => 'Review user accounts and permissions', 'time' => 3],
            ['task' => 'Check for malware and vulnerabilities', 'time' => 5],
            ['task' => 'Update security plugins', 'time' => 2],
            ['task' => 'Review file permissions', 'time' => 2],
            ['task' => 'Check SSL/TLS configuration', 'time' => 2],
            ['task' => 'Review security logs', 'time' => 3],
            ['task' => 'Implement security recommendations', 'time' => 5],
            ['task' => 'Document findings and actions taken', 'time' => 2],
        ]);

        // Performance Optimization
        $performance = MaintenanceReportType::create([
            'tenant_id' => $tenantId,
            'name' => 'Performance Optimization',
            'description' => 'Speed and performance improvements',
            'footer_text' => 'PERFORMANCE OPTIMIZATION REPORT',
            'is_active' => true,
        ]);

        $this->createMaintenanceTasks($performance->id, $tenantId, [
            ['task' => 'Run performance baseline tests', 'time' => 2],
            ['task' => 'Optimize images', 'time' => 3],
            ['task' => 'Minify CSS and JavaScript', 'time' => 2],
            ['task' => 'Configure caching', 'time' => 3],
            ['task' => 'Optimize database queries', 'time' => 4],
            ['task' => 'Enable lazy loading', 'time' => 2],
            ['task' => 'Configure CDN', 'time' => 3],
            ['task' => 'Run post-optimization tests', 'time' => 2],
            ['task' => 'Document improvements', 'time' => 1],
        ]);

        // Other
        $other = MaintenanceReportType::create([
            'tenant_id' => $tenantId,
            'name' => 'Other',
            'description' => 'Custom maintenance tasks',
            'footer_text' => 'MAINTENANCE REPORT',
            'is_active' => true,
        ]);

        $this->createMaintenanceTasks($other->id, $tenantId, [
            ['task' => 'Define maintenance scope', 'time' => 2],
            ['task' => 'Backup website', 'time' => 2],
            ['task' => 'Perform maintenance tasks', 'time' => 5],
            ['task' => 'Test and verify', 'time' => 3],
            ['task' => 'Send report', 'time' => 1],
        ]);
    }

    /**
     * Create maintenance tasks for a report type.
     */
    private function createMaintenanceTasks(int $reportTypeId, int $tenantId, array $tasks): void
    {
        foreach ($tasks as $index => $task) {
            MaintenanceTaskTemplate::create([
                'tenant_id' => $tenantId,
                'report_type_id' => $reportTypeId,
                'task_name' => $task['task'],
                'task_description' => null,
                'estimated_time_minutes' => $task['time'],
                'display_order' => $index + 1,
                'is_active' => true,
            ]);
        }
    }
}
