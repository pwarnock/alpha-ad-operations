# Alpha Reports

Advanced reporting package for SaaSykit applications, providing enterprise-grade analytics with multi-tenant security and subscription-based features.

## Features

- 🎯 **Advanced Filtering**: Multi-select dimensions (Advertiser, Campaign, Line Item, Geography, Device)
- 📈 **Interactive Charts**: Line, bar, and pie charts with Chart.js
- 🔒 **Tenant Isolation**: Complete multi-tenant data security
- 💳 **Subscription Controls**: Feature access based on subscription tiers
- 📤 **Export Options**: PDF and Excel with custom branding
- ⚡ **Performance Optimized**: Database indexing and caching strategies
- 🔧 **Developer Friendly**: Extensible architecture with comprehensive APIs

## Installation

```bash
composer require alpha/reports
```

## Quick Start

```bash
# Publish configuration
php artisan vendor:publish --provider="Alpha\\Reports\\ReportsServiceProvider"

# Run migrations
php artisan migrate

# Publish assets (optional)
php artisan vendor:publish --tag="reports-assets" --force
```

## Usage

### Basic Report Creation

```php
use Alpha\\Reports\\Services\\ReportBuilderService;

$builder = app(ReportBuilderService::class);

$report = $builder->generateReport([
    'filters' => [
        'date_range' => [
            'start' => '2025-10-01',
            'end' => '2025-10-31'
        ],
        'campaigns' => [1, 2, 3],
        'metrics' => ['impressions', 'clicks', 'revenue', 'ctr']
    ],
    'grouping' => 'day'
]);
```

### Chart Integration

```blade
<x-alpha-reports::report-chart
    :data="$reportData"
    type="line"
    :options="['responsive' => true]" />
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag="reports-config"
```

Key configuration options in `config/reports.php`:

```php
return [
    'max_reports_per_tenant' => 50,
    'cache' => [
        'ttl' => 3600,
        'driver' => 'redis',
    ],
    'features' => [
        'advanced_filters' => 'professional',
        'chart_visualization' => 'professional',
        'api_access' => 'professional',
    ],
];
```

## SaaSykit Integration

This package is designed to work seamlessly with SaaSykit applications:

- **Tenant Isolation**: All queries automatically scoped to current tenant
- **Subscription Features**: Feature access based on subscription tier
- **Event Integration**: Fires SaaSykit-compatible events
- **User Management**: Respects SaaSykit user permissions

## API Endpoints

### List Reports

```http
GET /api/reports
Authorization: Bearer {token}
```

### Generate Report

```http
POST /api/reports/{id}/generate
Authorization: Bearer {token}
```

### Export Report

```http
GET /api/reports/{id}/export/{format}
Authorization: Bearer {token}
```

## Testing

```bash
composer test
```

## Contributing

1. Fork the repository
2. Create feature branch
3. Make your changes
4. Add tests
5. Run test suite
6. Submit pull request

## License

This package is licensed under the MIT License. See [LICENSE](LICENSE) for details.

## Support

- **Documentation**: [Full Documentation](https://docs.alpha.com/reports)
- **Issues**: [GitHub Issues](https://github.com/alpha/reports/issues)
- **Email**: support@alpha.com

## Changelog

### [Unreleased]

- Initial release
- Advanced filtering system
- Chart visualization
- SaaSykit integration
- Export functionality

---

_Built with ❤️ for the SaaSykit ecosystem_
