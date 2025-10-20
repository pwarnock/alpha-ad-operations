# Alpha Reports Package

## Development Setup

This directory contains the Alpha Reports package for SaaSykit applications.

### Quick Start

```bash
# Install dependencies
composer install

# Run tests
composer test

# Format code
composer format

# Static analysis
composer analyse
```

### Development Workflow

1. Make changes in `src/`
2. Add tests in `tests/`
3. Run `composer test` to verify
4. Commit changes
5. Update version in `composer.json`
6. Create release tag

### Testing with Mock Application

The package can be tested with the mock application in the parent directory:

```bash
# From parent directory
composer require alpha/reports
php artisan vendor:publish --provider="Alpha\\Reports\\ReportsServiceProvider"
php artisan migrate
```

### Package Structure

- `src/` - Package source code
- `tests/` - Test suite
- `resources/` - Frontend assets
- `config/` - Package configuration

### Versioning

This package follows Semantic Versioning (SemVer).

### License

MIT License - see LICENSE file for details.
