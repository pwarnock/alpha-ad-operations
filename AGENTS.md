# Agent Guidelines for Laravel Ad Operations SaaS

## Build Commands

This project uses standard Laravel commands:

- `php artisan serve` - Start development server
- `php artisan test` - Run all tests
- `php artisan test --filter TestName` - Run single test
- `composer install` - Install dependencies
- `npm install && npm run build` - Build frontend assets
- `php artisan migrate` - Run database migrations

## Beads Task Management

This project uses Beads for atomic task tracking:

- Use `beads list` to view available tasks
- Use `beads ready` to see tasks ready for work
- Use `beads show <task-id>` for task details
- Use `beads update <task-id>` to mark progress
- Break complex features into small, atomic Beads tasks

## Git Workflow

This project follows trunk-based development:

- **main**: Protected branch, never commit directly
- **dev**: Integration branch, merge features here
- **feature/\***: Short-lived feature branches
- **CRITICAL**: Never git push without explicit user consent
- Use semantic versioning tags (v1.0.0, v1.1.0, etc.)
- Pre-commit hooks prevent main branch commits

## Code Style Guidelines

### Laravel & Filament Conventions

- Use Laravel 11+ conventions with Filament 3.x admin panels
- Follow PSR-12 coding standards
- Use type hints for all method parameters and return types
- Prefer Eloquent relationships over raw queries

### Import Organization

```php
// External libraries first
use Illuminate\Http\Request;
use Filament\Forms\Form;

// Internal application imports
use App\Models\Campaign;
use App\Filament\Resources\AdvertiserResource;
```

### Naming Conventions

- Models: PascalCase (Campaign, Advertiser)
- Controllers: PascalCase + "Controller" suffix
- Database tables: snake_case (campaigns, line_items)
- Routes: kebab-case for URLs, PascalCase for controller methods
- Filament Resources: PascalCase + "Resource" suffix

### Error Handling

- Use Laravel's built-in exception handling
- Validate all user input with Form Requests
- Return proper HTTP status codes (200, 404, 422, 500)
- Log errors with context for debugging

### Database & Models

- Use migrations for all schema changes
- Define relationships in models (belongsTo, hasMany, etc.)
- Use accessors/mutators for data formatting
- Implement proper indexing for performance

### Filament Specific

- Use Filament's form builder for all admin forms
- Implement proper resource policies
- Use table actions for row-level operations
- Leverage Filament's built-in components over custom UI

### Testing

- Write feature tests for all user workflows
- Test Filament resources with proper assertions
- Use factories for test data generation
- Test both success and failure scenarios

### Security

- Never commit secrets or API keys
- Use Laravel's built-in CSRF protection
- Implement proper authorization gates
- Validate and sanitize all user input

### Git Safety Rules

- **CRITICAL**: Never git push without explicit user consent
- Always ask for confirmation before any git push operation
- Use dry-run flags to preview push operations
- Verify remote branch before pushing
