# Laravel Business Management System - Replit Setup

## Project Overview
This is a comprehensive Laravel 12 business management application designed for construction and project management companies. The system includes modules for:

- **Project Management**: Projects, tasks, client management
- **Asset Management**: Equipment, locations, vendors, QR codes
- **Employee Management**: Staff, site allocations, roles
- **Financial Management**: Invoices, estimates, expenses, CIS payments
- **Health & Safety**: RAMS, incidents, observations, toolbox talks
- **Document Management**: File uploads, attachments, versioning
- **Time Tracking**: Clock in/out, timesheet management
- **Operative Management**: Invoicing, data forms, hire requests

## Recent Changes (Sep 21, 2025)
- **Major Performance Optimization**: Comprehensive loading time improvements implemented across the platform
- **Database Query Optimization**: Fixed N+1 query problems in ProjectController and SiteController using withCount() and selective eager loading instead of heavy collection loading
- **Database Indexing**: Added 20+ critical indexes on foreign keys, pivot tables, and filtering columns for dramatically faster queries
- **Caching Strategy**: Implemented 5-minute caching for filter dropdowns (sites, clients, managers) with company-scoped cache keys
- **Laravel Optimizations**: Enabled config:cache, route:cache, and view:cache for production performance
- **Frontend Asset Optimization**: Built production Vite assets with minification (CSS: 31KB gzipped, JS: 39KB gzipped)
- **Expected Impact**: 60-80% reduction in database queries and 40-60% faster page load times for listing pages

## Previous Changes (Sep 20, 2025)
- **Enhanced Manager Site Views**: Updated manager site view to include all admin functionalities (project management, filtering, status updates, statistics) while excluding all financial data for proper role-based access control
- **Complete Project Management**: Managers now have advanced project filtering, real-time status updates, archive management, and detailed project tables identical to admin functionality
- **Visual Consistency**: Manager interface now matches admin design and functionality while maintaining strict separation of financial information
- Fixed database column name issue: changed "assigned_user_id" to "assigned_to" in OperativeDashboardController
- Resolved time_entries table schema mismatch by adding missing columns (clock_in, clock_out, site_id, notes, location, etc.)
- Updated time_entries status enum to support TimeEntry model requirements (active, completed, approved, rejected)
- Fixed operative dashboard database errors - now loads correctly
- Ensured time tracking functionality works with proper database schema

## Previous Changes (Sep 19, 2025)
- Successfully imported from GitHub and configured for Replit environment
- Set up PHP 8.2 and Node.js 20 development environment
- Configured PostgreSQL database with 90+ migration files
- Fixed migration dependency issues with foreign key constraints
- Built frontend assets with Vite and Bootstrap/Tailwind CSS
- Configured Laravel development server on port 5000
- Set up production deployment configuration

## Project Architecture
- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Blade templates with Vite, Bootstrap 5, Tailwind CSS
- **Database**: PostgreSQL (configured with Neon)
- **Build System**: Vite for asset compilation
- **Package Management**: Composer (PHP), npm (JavaScript)

## Environment Setup
- Database: PostgreSQL with environment variables
- Sessions: File-based (for Replit compatibility)
- Cache: Database-backed
- Mail: Log driver (development)
- App URL: Configured for 0.0.0.0:5000 (Replit proxy compatible)

## Key Features
The application includes comprehensive modules for:
1. Multi-tenant company management
2. Role-based access control (Admin, Manager, Operative, etc.)
3. Project lifecycle management
4. Asset tracking with QR codes
5. CIS (Construction Industry Scheme) compliance
6. Financial reporting and expense management
7. Health & Safety documentation
8. Employee time tracking and payroll
9. Document version control
10. Email notifications and reporting

## Technical Notes
- Fixed PostgreSQL migration dependencies by removing foreign key constraints from initial table creation
- Configured Vite to allow all hosts for Replit proxy compatibility
- Set up workflow to run Laravel development server with database environment variables
- Production deployment configured for autoscale deployment target