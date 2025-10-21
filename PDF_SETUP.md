# PDF Export Setup Guide

This document covers PDF export functionality setup and troubleshooting for the Alpha Ad Operations platform.

## Overview

The platform uses **Barryvdh DomPDF** for generating PDF reports. DomPDF is a lightweight PHP-based PDF generation library that converts HTML/CSS to PDF without requiring external dependencies like Node.js or Chrome.

## Dependencies

### Required Packages

```json
{
  "require": {
    "barryvdh/laravel-dompdf": "^3.1"
  }
}
```

### Installation

```bash
# Install PHP dependencies (includes DomPDF)
composer install

# Install Node.js dependencies (for frontend assets only)
npm install
```

## System Requirements

### Development

- PHP 8.2+
- Standard Laravel requirements
- No additional dependencies required

### Production

- PHP 8.2+
- Standard Laravel requirements
- No external dependencies or system libraries required

## Configuration

### Environment Variables

```env
# Puppeteer configuration (optional)
PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=false  # Set to true if using system Chrome
PUPPETEER_EXECUTABLE_PATH=/path/to/chrome  # Custom Chrome path
```

### PDF Configuration

The PDF export is configured in `app/Http/Controllers/ReportController.php`:

```php
$pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
return $pdf->download($report->name . '.pdf');
```

## Usage

### Basic PDF Export

1. Navigate to Reports → Saved Reports
2. Click "Run Report" on any saved report
3. Click "Export PDF" button
4. PDF downloads automatically

### Available Options

- **Format**: A4 (configurable)
- **Orientation**: Portrait (default)
- **Margins**: Default DomPDF settings
- **Background**: Enabled for styling
- **Header/Footer**: Disabled (configurable)
- **CSS Support**: Full CSS2.1 support
- **JavaScript**: Not supported (use HTML export for JS features)

## Troubleshooting

### Common Issues

#### 1. "PDF generation failed"

```php
// Solution: Check HTML content and CSS
// DomPDF is sensitive to invalid HTML/CSS
// Try simplifying complex layouts
```

#### 2. "Font not found"

```php
// Solution: Use standard fonts or embed custom fonts
// DomPDF supports: Arial, Helvetica, Times New Roman, Courier
$pdf->setOptions(['defaultFont' => 'Arial']);
```

#### 3. "Memory limit exceeded"

```php
// Solution: Increase PHP memory limit
ini_set('memory_limit', '256M');
// Or optimize HTML content
```

#### 4. "CSS not applying"

```php
// Solution: Use inline styles or valid CSS
// DomPDF has limited CSS support
// Avoid modern CSS features
```

#### 2. "No usable sandbox!"

```bash
# Solution: Run with no-sandbox flag (in production)
# Add to PDF configuration:
->setOption('args', ['--no-sandbox', '--disable-setuid-sandbox'])
```

#### 3. Memory issues in production

```bash
# Solution: Limit Chrome processes
# Add to server configuration:
PUPPETEER_ARGS="--max-old-space-size=512"
```

#### 4. Timeout issues

```php
// Solution: Increase timeout in PDF configuration
->setOption('timeout', 30000)  // 30 seconds
```

### Debug Mode

Enable debugging for PDF generation:

```php
// In ReportController.php
$pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
return $pdf->stream($report->name . '.pdf');  // Opens in browser instead of downloading
```

### Fallback Options

If PDF generation fails, system provides:

1. **HTML Export**: Direct HTML download (preserves JavaScript functionality)
2. **Excel Export**: Alternative format for data analysis
3. **Browser Print**: Use browser's print functionality from HTML export

## Performance Optimization

### Production Tips

1. **Memory Management**: Monitor PHP memory usage
2. **Caching**: Cache generated PDFs when possible
3. **Queueing**: Use Laravel queues for large report generation
4. **HTML Optimization**: Simplify HTML for faster processing

### Example Production Configuration

```php
// Optimized PDF generation for production
$pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
$pdf->setOptions([
    'defaultFont' => 'Arial',
    'isRemoteEnabled' => false,  // Security improvement
    'isHtml5ParserEnabled' => true,
    'isPhpEnabled' => false,  // Security improvement
]);
return $pdf->download($report->name . '.pdf');
```

## Security Considerations

- **PHP Execution**: Disable PHP code execution in PDFs (`isPhpEnabled: false`)
- **Remote Content**: Disable remote content loading (`isRemoteEnabled: false`)
- **File Access**: Limit file system access for PDF generation
- **Memory**: Monitor memory usage to prevent DoS attacks

## Deployment Notes

### Docker

```dockerfile
# No special requirements needed
# DomPDF works with standard PHP Docker images
FROM php:8.2-fpm
# Standard Laravel Docker setup works fine
```

### Laravel Cloud

Laravel Cloud automatically handles DomPDF installation through `composer install`.

### Traditional Hosting

Ensure PHP 8.2+ is available with standard Laravel requirements. No additional dependencies needed.

## Testing

```bash
# Test PDF generation
php artisan tinker
>>> $report = App\Models\SavedReport::first();
>>> app('App\Http\Controllers\ReportController')->exportPdf($report->id);
```

## Support

For PDF-related issues:

1. Check DomPDF documentation: https://github.com/dompdf/dompdf
2. Review Barryvdh Laravel DomPDF docs: https://github.com/barryvdh/laravel-dompdf
3. Verify HTML/CSS validity (common issue source)
4. Check Laravel logs for detailed error messages
5. Test with simpler HTML to isolate issues

---

**Last Updated**: October 21, 2025
**Version**: 2.0.0 (DomPDF implementation)
**Previous Version**: 1.0.0 (Puppeteer implementation)
