# Tracklyt

**Tracklyt** is a multi-tenant Laravel SaaS for developers, agencies, and maintenance companies.  
It manages the **full lifecycle of work**: **Quote → Project → Delivery → Maintenance → Billing**, with comprehensive time tracking, maintenance checklists, and reporting.

---

## ✨ Key Features Implemented

### 🎯 Quotes Management
- Complete quote lifecycle (draft → sent → approved)
- Line items with categories and hourly rates
- Tech stack assumptions tracking
- Professional PDF generation with table layout
- Client-specific quote templates

### 📁 Projects & Delivery
- Full project CRUD with status tracking
- **Project Repositories** - GitHub/GitLab integration tracking
- **Project Links** - Demo, staging, production URLs
- **Mobile App Metadata** - Play Store & App Store tracking
- Inline modal-based management for all relations
- Project types: New Build or Maintenance Takeover

### ✅ Task Management
- Task status workflow (todo → in_progress → completed)
- Priority levels (low, medium, high)
- Task assignment to team members
- Due dates with overdue tracking
- Complete/reopen functionality
- Project-based task organization

### ⏱️ Time Tracking
- **Smart Timer** - Start/stop with running timer prevention
- **Navbar Timer Badge** - Live countdown visible everywhere
- **Dashboard Timer** - Real-time updates on home screen
- Manual time entry with auto-duration calculation
- Billable/non-billable tracking
- Admin lock/unlock functionality
- Comprehensive filtering (project, task, user, date range, status)
- Time entry notes and history

### 🔧 Maintenance Reports
- **8 Report Types** - Web Maintenance, Migration, Updates, Fixes, Security, Performance, etc.
- **70+ Task Templates** - Pre-defined checklists for each report type
- Task execution with comments and screenshots (JSON array)
- Progress tracking with completion percentage
- Auto-generated report numbers (MAINT-2025-001)
- Status workflow (draft → in_progress → completed → sent)
- Time tracking per task

### 🏢 Multi-Tenancy
- Logical tenant isolation with `tenant_id`
- Secure cross-tenant data prevention
- Single database architecture
- Tenant-scoped queries via traits

---

## Core Technology Stack

- **Backend**: Laravel 11 with PHP 8.2+
- **UI**: Bootstrap 5 with custom Tracklyt theme
- **Database**: MySQL 8+ with tenant-aware tables
- **PDF Generation**: DomPDF for quotes and reports
- **Time Tracking**: Real-time JavaScript timers
- **File Storage**: Laravel Storage with JSON screenshot arrays

---

## Installation

### Prerequisites
- PHP 8.2+  
- Composer 2.6+  
- MySQL 8+  
- Node.js 20+  

### Steps

```bash
# Clone repo
git clone https://github.com/<username>/tracklyt.git
cd tracklyt

# Install PHP & JS dependencies
composer install
npm install && npm run dev

# Copy .env and generate key
cp .env.example .env
php artisan key:generate

# Configure database in .env
DB_DATABASE=tracklyt
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Run migrations
php artisan migrate

# Seed maintenance report types (optional)
php artisan db:seed --class=MaintenanceReportTypeSeeder

# Start server
php artisan serve
