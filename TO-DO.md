# Tracklyt — TO-DO / SPECIFICATION

A multi-tenant Laravel SaaS for developers, agencies, and maintenance companies  
Track work from **Quote → Project → Delivery → Maintenance → Billing**

---

## 1. PRODUCT GOAL

Tracklyt is a single source of truth for:
- Quotes & technical assumptions
- Project delivery & progress
- Time tracking & reporting
- Maintenance & retainers
- Invoicing & billing

Built SaaS-first, single database, multi-tenant.

---

## 2. CORE ARCHITECTURE

### 2.1 Tenancy Model
- Single database
- Logical tenancy using `tenant_id`
- One tenant = one business
- No cross-tenant access

### 2.2 Rules
- All business tables **must include `tenant_id`**
- All queries **must be tenant-scoped**
- Users belong to exactly one tenant

---

## 3. AUTH & ACCESS CONTROL

### 3.1 Users
Roles:
- Owner
- Admin
- Staff
- Client (read-only, optional)

Rules:
- Owner: full system + billing
- Admin: manage users, projects, quotes
- Staff: time tracking only
- Client: view-only

Use:
- Laravel Sanctum
- Policies
- Middleware

---

## 4. CORE DOMAIN MODELS

### 4.1 Tenants
- id
- name
- slug
- plan
- status
- billing_email
- created_at
- updated_at

---

### 4.2 Users
- id
- tenant_id
- name
- email
- password
- role
- hourly_rate (nullable)
- is_active
- last_login_at
- created_at
- updated_at

---

### 4.3 Clients
- id
- tenant_id
- name
- email
- phone
- notes
- is_active
- created_at
- updated_at

---

## 5. QUOTE LIFECYCLE

### 5.1 Quotes
- id
- tenant_id
- client_id
- quote_number
- title
- description
- status (draft, sent, approved, rejected, expired)
- estimated_hours
- estimated_cost
- currency
- valid_until
- created_by
- created_at
- updated_at

---

### 5.2 Quote Line Items
- id
- tenant_id
- quote_id
- category (dev, design, testing, maintenance)
- description
- hours
- rate
- total
- created_at
- updated_at

---

### 5.3 Quote Tech Stack
(Explicit assumptions)

- id
- tenant_id
- quote_id
- language
- framework
- database
- hosting
- third_party_services (JSON)
- notes
- created_at
- updated_at

---

### 5.4 Quote Rules
- Quotes can be edited only in `draft`
- Approved quotes are locked
- Approved quotes can be converted into projects

### ✅ 5.5 Status: COMPLETED
- Full CRUD implementation
- PDF generation with professional table layout
- Line items and tech stack management
- Quote lifecycle (draft → sent → approved → rejected)

---

## 6. PROJECTS

### 6.1 Projects
- id
- tenant_id
- client_id
- quote_id (nullable)
- name
- description
- project_type (new_build, maintenance_takeover)
- source (quote, direct)
- billing_type (hourly, fixed, retainer)
- hourly_rate
- estimated_hours
- start_date
- end_date
- is_active
- created_at
- updated_at

Rules:
- Projects may exist without quotes
- Maintenance takeover projects skip quotes

### ✅ 6.2 Status: COMPLETED
- Full CRUD with all views (index, create, edit, show)
- Project repositories inline management
- Project links inline management
- Mobile app metadata inline management
- Modal-based CRUD for related entities

---

## 7. DELIVERY & VISIBILITY

### 7.1 Project Repositories
- id
- tenant_id
- project_id
- provider (github, gitlab, bitbucket)
- repo_url
- is_primary
- created_at
- updated_at

---

### 7.2 Project Links
- id
- tenant_id
- project_id
- type (demo, staging, production)
- label
- url
- created_at
- updated_at

---

### 7.3 Mobile App Metadata (Optional)
- id
- tenant_id
- project_id
- platform (android, ios)
- app_name
- package_name
- app_store_url
- play_store_url
- current_version
- created_at
- updated_at

---

## 8. TIME TRACKING

### 8.1 Tasks
- id
- tenant_id
- project_id
- name
- description
- is_billable
- created_at
- updated_at

---

### 8.2 Time Entries
- id
- tenant_id
- user_id
- project_id
- task_id
- start_time
- end_time
- duration_minutes
- is_billable
- notes
- locked_at
- created_at
- updated_at

Rules:
- Locked entries cannot be edited
- Entries lock automatically when invoiced

### ✅ 8.3 Status: COMPLETED
- **Tasks**: Full CRUD with status (todo, in_progress, completed), priority, assignment, due dates
- **Time Entries**: Full CRUD with timer functionality
- **Timer Features**: Start/stop timer, running timer prevention, navbar timer badge, dashboard timer display
- **Filtering**: Comprehensive filters (project, task, user, billable, locked, date range)
- **Locking**: Admin lock/unlock functionality
- **Real-time**: Live timer updates via JavaScript

---

## 9. MAINTENANCE & RETAINERS

### 9.1 Maintenance Profiles
- id
- tenant_id
- project_id
- maintenance_type (retainer, hourly)
- monthly_hours
- rate
- sla_notes
- rollover_hours
- start_date
- created_at
- updated_at

Rules:
- Required for `maintenance_takeover` projects
- Retainers reset monthly

---

### 9.2 Maintenance Report Types (NEW)
- id
- tenant_id
- name (e.g., "Web Maintenance Report", "Web Move Report")
- description
- report_image (logo/header)
- footer_text
- is_active
- created_at
- updated_at

---

### 9.3 Maintenance Task Templates
- id
- tenant_id
- report_type_id
- task_item (task description)
- estimated_time (minutes)
- display_order
- is_active
- created_at
- updated_at

---

### 9.4 Maintenance Reports
- id
- tenant_id
- project_id
- report_type_id
- created_by
- report_number (auto: MAINT-2025-001)
- maintenance_date
- status (draft, in_progress, completed, sent)
- notes
- completed_at
- sent_at
- created_at
- updated_at

---

### 9.5 Maintenance Report Tasks
- id
- tenant_id
- report_id
- task_item
- comment (work notes)
- screenshots (JSON array)
- time_spent (minutes)
- display_order
- is_completed
- completed_at
- created_at
- updated_at

### ✅ 9.6 Status: PARTIALLY COMPLETED
- ✅ Database structure (4 new tables)
- ✅ Models with relationships
- ✅ Seeder with 8 report types and 70+ task templates
- ⏳ Controller and views (pending)
- ⏳ PDF report generation (pending)

---

## 10. BILLING & INVOICING

### 10.1 Invoices
- id
- tenant_id
- client_id
- project_id
- invoice_number
- status (draft, sent, paid, overdue)
- issue_date
- due_date
- subtotal
- tax
- total
- currency
- created_at
- updated_at

---

### 10.2 Invoice Items
- id
- tenant_id
- invoice_id
- time_entry_id (nullable)
- description
- quantity
- unit_price
- total
- created_at
- updated_at

---

## 11. REPORTING (GENERATED)

- Time per project
- Billable vs non-billable
- Client profitability
- Retainer usage
- Team utilization

(No persistent tables)

---

## 12. CLIENT PORTAL (PHASE 3)

Clients can:
- View quote status
- View project links & repos
- View invoices
- Download PDFs
- Whatsapp intergration

(Read-only)

### ✅ 12.1 Status: COMPLETED
- **Magic Link Authentication**: Email and WhatsApp-based passwordless login
- **Client Dashboard**: Overview with stats for quotes, projects, invoices
- **Quotes Access**: View and download quote PDFs
- **Projects Access**: View project details, repositories, links, mobile apps
- **Invoices Access**: View and download invoice PDFs
- **Security**: Time-limited tokens (24h expiry), one-time use
- **Twilio Integration**: WhatsApp message delivery for magic links

---

## 13. SAAS & BILLING (PHASE 4)

Plans:
- Trial
- Freelancer
- Agency
- Enterprise

Limits:
- Users
- Projects
- Monthly tracked hours
- Invoice count

---

## 14. TENANT RESOLUTION

- Subdomain or tenant selector
- Middleware-based resolution
- Global Eloquent scope

---

## 15. DEVELOPMENT PHASES

### Phase 1 — Foundation
- Auth
- Tenancy
- Clients
- Quotes
- Projects

### Phase 2 — Time Tracking
- Tasks
- Time entries
- Locking logic

### Phase 3 — Billing
- Invoices
- PDF export

### Phase 4 — Reporting
- Dashboards
- KPIs

### Phase 5 — SaaS
- Plans
- Limits
- Client portal

---

## 16. MIGRATION STRATEGY

- Import tenants
- Map users → tenants
- Convert existing projects
- Normalize time entries
- Validate invoice totals

---

## 17. NON-FUNCTIONAL REQUIREMENTS

- Soft deletes where needed
- Audit critical actions
- Background jobs for heavy tasks
- Secure tenant isolation

---

## 18. FUTURE EXTENSIONS

- GitHub API sync
- Mobile app
- White-label mode
- Dedicated DB per enterprise tenant

---

## 19. IMPLEMENTATION STATUS

### ✅ COMPLETED MODULES
1. **Quotes** - Full CRUD, PDF generation, line items, tech stack
2. **Projects** - Full CRUD, repositories, links, mobile apps (inline management)
3. **Tasks** - Full CRUD, status management, assignment, priorities, due dates
4. **Time Entries** - Full CRUD, timer, locking, filtering, navbar badge
5. **Maintenance Report Types** - Database structure, models, seeder
6. **Invoices** - Full CRUD, PDF generation, time entry integration
7. **Reporting** - Dashboards, KPIs, charts
8. **Client Portal** - Magic link auth (email/WhatsApp), read-only access to quotes/projects/invoices

### 🔄 IN PROGRESS
1. **Maintenance Reports** - Execution interface, task completion, PDF generation

### ⏳ PENDING
1. **SaaS Billing** - Subscription plans and limits

### 🎯 CURRENT PHASE: Phase 3 → Phase 4 Transition
- Phase 1 (Foundation): ✅ Complete
- Phase 2 (Time Tracking): ✅ Complete
- Phase 3 (Billing & Client Portal): ✅ Complete
- Phase 4 (Reporting): ✅ Complete
- Phase 5 (SaaS): ⏳ Pending

---

END OF DOCUMENT
