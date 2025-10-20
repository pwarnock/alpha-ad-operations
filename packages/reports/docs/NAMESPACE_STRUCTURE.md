# PSR-4 Namespace Structure

## Base Namespace

`Alpha\Reports\`

## Autoloading Configuration

```json
{
  "autoload": {
    "psr-4": {
      "Alpha\\Reports\\": "src/"
    }
  },
  "autoload-dev": {
    "psr-4": {
      "Alpha\\Reports\\Tests\\": "tests/"
    }
  }
}
```

## Namespace Hierarchy

### Core Services

```
Alpha\Reports\Services\
├── ReportBuilderService.php      # Main report generation logic
├── ChartDataService.php         # Data transformation for charts
└── SaaSykitReportsService.php   # SaaSykit compatibility layer
```

### Models

```
Alpha\Reports\Models\
└── SavedReport.php              # Package-specific report model
```

### HTTP Layer

```
Alpha\Reports\Http\
├── Controllers\
│   └── ReportController.php     # Web and API endpoints
└── Middleware\
    └── TenantScope.php         # Multi-tenant isolation
```

### Filament Integration

```
Alpha\Reports\Filament\
└── Resources\
    └── SavedReportResource.php  # Admin panel interface
```

### Views & Components

```
Alpha\Reports\Views\
├── components\
│   ├── report-builder.blade.php  # Advanced filtering UI
│   └── report-chart.blade.php    # Chart visualization
└── reports\
    └── show.blade.php            # Report display
```

### Configuration

```
Alpha\Reports\Config\
└── reports.php                 # Package configuration
```

### Database

```
Alpha\Reports\Database\
└── Migrations\
    ├── create_reports_table.php
    └── create_report_subscriptions_table.php
```

### Contracts

```
Alpha\Reports\Contracts\
└── ReportBuilderServiceInterface.php  # Service interface
```

### Events

```
Alpha\Reports\Events\
├── ReportGenerated.php         # Report completion event
└── ReportShared.php           # Report sharing event
```

### Jobs

```
Alpha\Reports\Jobs\
└── GenerateReportJob.php      # Background report generation
```

## Testing Namespace

```
Alpha\Reports\Tests\
├── Unit\
│   ├── Services\
│   ├── Models\
│   └── Controllers\
└── Feature\
    ├── ReportGenerationTest.php
    └── SaaSykitIntegrationTest.php
```

## Class Loading Examples

### Service Class

```php
<?php
// File: src/Services/ReportBuilderService.php
namespace Alpha\Reports\Services;

class ReportBuilderService
{
    // Implementation
}
```

### Controller Class

```php
<?php
// File: src/Http/Controllers/ReportController.php
namespace Alpha\Reports\Http\Controllers;

use Alpha\Reports\Services\ReportBuilderService;

class ReportController
{
    // Implementation
}
```

### Test Class

```php
<?php
// File: tests/Unit/Services/ReportBuilderServiceTest.php
namespace Alpha\Reports\Tests\Unit\Services;

use Alpha\Reports\Services\ReportBuilderService;
use Alpha\Reports\Tests\TestCase;

class ReportBuilderServiceTest extends TestCase
{
    // Implementation
}
```

## Benefits of This Structure

1. **PSR-4 Compliant**: Standard autoloading
2. **Clear Separation**: Logical grouping of functionality
3. **Extensible**: Easy to add new components
4. **Testable**: Separate test namespace
5. **Laravel Conventions**: Follows Laravel package patterns
