# Alpha Reports Package

This package provides advanced reporting capabilities for SaaSyKit platform.

## Installation

1. Add the package to your composer.json or install via composer:
   ```
   composer require alpha/reports
   ```

2. Publish the service provider:
   ```
   php artisan vendor:publish --provider="Alpha\\Reports\\ReportsServiceProvider"
   ```

3. Run migrations:
   ```
   php artisan migrate
   ```

4. (Optional) Publish config:
   ```
   php artisan vendor:publish --tag=reports-config
   ```

## Usage

### Basic Reporting

Visit `/reports` to access the report builder.

Filter by:
- Rep ID
- Advertiser ID
- Product (Line Item name)
- Date range

### Export

Reports can be exported to PDF or Excel.

## API

The package provides the following routes:

- `GET /reports` - Report index
- `GET /reports/generate` - Generate report
- `GET /reports/export/{format}` - Export report (pdf/excel)

## Filament Integration

The package includes Filament resources for managing saved reports.

## Marketing Pages

The main application includes public marketing pages:
- `/` - Home
- `/pricing` - Pricing
- `/register` - Registration

These are static pages that can be edited by developers.

## Configuration

Edit `config/reports.php` to customize default filters and export formats.

## Handoff Notes

- Reporting logic is in `ReportController`
- Views are in `resources/views`
- Models: `SavedReport`
- Exports: `ReportExport`

For marketing pages, edit the views in `resources/views/marketing/`
