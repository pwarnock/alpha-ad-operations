# 📦 Project Structure Setup

This document outlines the dual-purpose project structure that supports both:

1. **Deliverable Plugin**: The `alpha/reports` Laravel package for distribution
2. **Mock Application**: Testing environment until customer codebase access

---

## 🏗️ Directory Structure

```
alpha/
├── 📦 Package (Deliverable)
│   └── packages/reports/
│       ├── src/
│       │   ├── Services/
│       │   │   ├── ReportBuilderService.php
│       │   │   ├── ChartDataService.php
│       │   │   └── SaaSykitReportsService.php
│       │   ├── Models/
│       │   │   └── SavedReport.php
│       │   ├── Http/
│       │   │   ├── Controllers/
│       │   │   │   └── ReportController.php
│       │   │   └── Middleware/
│       │   │       └── TenantScope.php
│       │   ├── Filament/
│       │   │   └── Resources/
│       │   │       └── SavedReportResource.php
│       │   ├── Views/
│       │   │   ├── components/
│       │   │   │   ├── report-builder.blade.php
│       │   │   │   └── report-chart.blade.php
│       │   │   └── reports/
│       │   │       └── show.blade.php
│       │   ├── Config/
│       │   │   └── reports.php
│       │   ├── Database/
│       │   │   └── Migrations/
│       │   │       ├── create_reports_table.php
│       │   │       └── create_report_subscriptions_table.php
│       │   ├── Contracts/
│       │   │   └── ReportBuilderServiceInterface.php
│       │   ├── Events/
│       │   │   ├── ReportGenerated.php
│       │   │   └── ReportShared.php
│       │   ├── Jobs/
│       │   │   └── GenerateReportJob.php
│       │   └── ReportsServiceProvider.php
│       ├── tests/
│       │   ├── Unit/
│       │   │   ├── Services/
│       │   │   ├── Models/
│       │   │   └── Controllers/
│       │   └── Feature/
│       │       ├── ReportGenerationTest.php
│       │       └── SaaSykitIntegrationTest.php
│       ├── resources/
│       │   ├── js/
│       │   │   ├── report-builder.js
│       │   │   └── report-charts.js
│       │   └── css/
│       │       └── reports.css
│       ├── composer.json
│       ├── README.md
│       └── LICENSE
│
├── 🧪 Mock Application (Testing)
│   ├── app/ (Current existing app)
│   │   ├── Models/ (Existing: Impression, Campaign, Advertiser, etc.)
│   │   ├── Http/Controllers/
│   │   ├── Filament/
│   │   └── Services/
│   │       └── SaaSykitPluginService.php (Existing)
│   ├── database/
│   │   ├── migrations/ (Existing)
│   │   └── factories/ (Existing)
│   ├── tests/ (Existing)
│   ├── composer.json (Modified to include local package)
│   └── .env (Testing configuration)
│
├── 📚 Documentation
│   ├── docs/
│   │   ├── REPORTS_PACKAGE_PLAN.md
│   │   ├── REPORTS_ROADMAP.md
│   │   └── REPORTS_ARCHITECTURE.md
│   └── exports/
│       ├── customer-proposal.md
│       ├── technical-specifications.md
│       └── project-timeline.md
│
└── 🔧 Development Tools
    ├── .github/
    │   └── workflows/
    │       ├── tests.yml
    │       └── release.yml
    ├── scripts/
    │   ├── setup.sh
    │   ├── test.sh
    │   └── build.sh
    └── docker/
        ├── package.Dockerfile
        └── mock-app.Dockerfile
```

---

## 📦 Package Structure (packages/reports/)

### **Core Components**

#### **Services Layer**

```php
src/Services/
├── ReportBuilderService.php      # Main query building and report generation
├── ChartDataService.php         # Data transformation for charts
└── SaaSykitReportsService.php   # SaaSykit compatibility layer
```

#### **Models Layer**

```php
src/Models/
└── SavedReport.php              # Package-specific report model
```

#### **HTTP Layer**

```php
src/Http/
├── Controllers/
│   └── ReportController.php     # Web and API endpoints
└── Middleware/
    └── TenantScope.php         # Multi-tenant isolation
```

#### **Filament Integration**

```php
src/Filament/
└── Resources/
    └── SavedReportResource.php  # Admin panel interface
```

#### **Views & Components**

```php
src/Views/
├── components/
│   ├── report-builder.blade.php  # Advanced filtering UI
│   └── report-chart.blade.php    # Chart visualization
└── reports/
    └── show.blade.php            # Report display
```

### **Package Configuration**

#### **composer.json**

```json
{
  "name": "alpha/reports",
  "description": "Advanced reporting package for SaaSykit applications",
  "type": "laravel-package",
  "license": "MIT",
  "require": {
    "php": "^8.2",
    "laravel/framework": "^11.0",
    "filament/filament": "^3.0"
  },
  "autoload": {
    "psr-4": {
      "Alpha\\Reports\\": "src/"
    }
  },
  "extra": {
    "laravel": {
      "providers": ["Alpha\\Reports\\ReportsServiceProvider"]
    }
  }
}
```

---

## 🧪 Mock Application Structure

### **Modified composer.json**

```json
{
  "require": {
    "alpha/reports": "dev-main",
    "laravel/framework": "^11.0",
    "filament/filament": "^3.0"
  },
  "repositories": [
    {
      "type": "path",
      "url": "./packages/reports",
      "options": {
        "symlink": true
      }
    }
  ]
}
```

### **Testing Configuration**

```env
# .env.testing
APP_ENV=testing
DB_CONNECTION=sqlite
DB_DATABASE=:memory:

# Package testing
REPORTS_CACHE_DRIVER=array
REPORTS_QUEUE_CONNECTION=sync
```

---

## 🔄 Development Workflow

### **Package Development**

1. **Work in**: `packages/reports/`
2. **Test locally**: Via mock application
3. **Commit**: Package changes independently
4. **Release**: As separate composer package

### **Mock Application Development**

1. **Work in**: Root directory
2. **Test package**: Via local path repository
3. **Integration**: Test SaaSykit compatibility
4. **Demo**: Show customer functionality

---

## 🚀 Build & Deployment

### **Package Build**

```bash
# In packages/reports/
composer install
composer test
composer build
```

### **Mock Application Setup**

```bash
# In root
composer install
php artisan migrate
php artisan vendor:publish --provider="Alpha\\Reports\\ReportsServiceProvider"
```

### **Testing Workflow**

```bash
# Run package tests
cd packages/reports && composer test

# Run integration tests
cd ../.. && php artisan test --filter="Reports*"

# Run full suite
composer test:all
```

---

## 📋 Setup Commands

### **Initial Setup**

```bash
# Create package structure
mkdir -p packages/reports/{src,tests,resources}

# Set up composer files
touch packages/reports/composer.json
touch packages/reports/README.md
touch packages/reports/LICENSE

# Configure mock application
composer config repositories.alpha '{"type": "path", "url": "./packages/reports", "options": {"symlink": true}}'
composer require alpha/reports
```

### **Development Scripts**

```bash
# scripts/setup.sh
#!/bin/bash
echo "Setting up Reports Package Development Environment..."

# Create directories
mkdir -p packages/reports/{src/{Services,Models,Http,Views,Config,Database},tests,resources/{js,css}}

# Create service provider
touch packages/reports/src/ReportsServiceProvider.php

# Update mock app composer
composer config repositories.alpha '{"type": "path", "url": "./packages/reports", "options": {"symlink": true}}'
composer require alpha/reports

echo "Setup complete! Start developing in packages/reports/"
```

---

## 🎯 Next Steps

1. **Create package skeleton** with all directories
2. **Set up composer configuration** for local development
3. **Create basic service provider** with Laravel integration
4. **Set up testing framework** with Testbench
5. **Begin Phase 1 development** following roadmap

---

_This structure enables parallel development of the distributable package while maintaining a functional mock application for testing and demonstration purposes._
