# Alpha Ad Operations - Laravel SaaS Platform

A comprehensive Laravel-based SaaS platform for managing advertising operations for media companies.

## Features

### Core Functionality

- **Campaign Management**: Create, manage, and optimize advertising campaigns
- **Advertiser Management**: Track advertiser relationships and performance
- **Line Item Management**: Manage individual ad placements and inventory
- **Impression Tracking**: Real-time performance data collection

### Custom Report Builder

- **Flexible Report Configuration**: Build custom reports with dynamic filters
- **Multiple Report Types**:
  - Advertiser Performance Reports
  - Inventory Reports
  - Campaign Delivery Reports
  - Revenue Reports
- **Export Options**: PDF and Excel export functionality
- **Saved Reports**: Save and reuse report configurations
- **Data Grouping**: Group by day, week, month, advertiser, or campaign

### Marketing Pages

- **Professional Home Page**: Hero section, features, testimonials
- **Pricing Page**: Annual/monthly toggle with feature comparison
- **Responsive Design**: Mobile-first design with Tailwind CSS

## Tech Stack

- **Backend**: Laravel 11+ with PHP 8.2+
- **Admin Panel**: Filament 3.x for admin interface
- **Frontend**: Blade templates with Tailwind CSS
- **Database**: SQLite (configurable for MySQL/PostgreSQL)
- **PDF Generation**: Barryvdh DomPDF (lightweight PHP solution)
- **Excel Export**: Laravel Excel (Maatwebsite)

## Installation

### Prerequisites

- PHP 8.2+
- Composer
- Node.js & NPM (for frontend assets)

### Setup Steps

1. **Clone the repository**

   ```bash
   git clone <repository-url>
   cd alpha-ad-operations
   ```

2. **Install dependencies**

   ```bash
   composer install
   npm install
   ```

3. **Environment setup**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**

   ```bash
   php artisan migrate
   ```

5. **Build frontend assets**

   ```bash
   npm run build
   ```

6. **Start development server**
   ```bash
   php artisan serve
   ```

## Configuration

### Environment Variables

Key environment variables to configure:

```env
APP_NAME="Alpha Ad Operations"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

### Admin Panel Access

- Navigate to `/admin` for the Filament admin panel
- Default user creation: Use Laravel's user creation commands

## Project Structure

```
├── app/
│   ├── Filament/Resources/     # Filament admin resources
│   ├── Http/Controllers/       # HTTP controllers
│   ├── Models/               # Eloquent models
│   └── Providers/           # Service providers
├── database/
│   ├── migrations/           # Database migrations
│   └── seeders/            # Database seeders
├── resources/
│   ├── views/
│   │   ├── components/       # Blade components
│   │   ├── layouts/         # Layout templates
│   │   └── marketing/       # Marketing page views
│   └── css/app.css         # Tailwind CSS
└── routes/
    └── web.php             # Web routes
```

## Available Commands

### Development

```bash
php artisan serve              # Start development server
php artisan migrate             # Run database migrations
php artisan migrate:fresh --seed  # Fresh migration with seeders
php artisan tinker              # Laravel REPL
```

### Testing

```bash
php artisan test              # Run all tests
php artisan test --filter TestName  # Run specific test
```

### Frontend

```bash
npm run dev                 # Start Vite development server
npm run build               # Build for production
npm run format              # Format with Prettier
npm run lint                # Run ESLint
```

### Beads Task Management

```bash
beads list                  # View available tasks
beads ready                 # See tasks ready for work
beads show <task-id>        # Show task details
beads update <task-id>      # Update task status
```

### Git Workflow

```bash
git checkout dev             # Switch to integration branch
git checkout -b feature/name # Create feature branch
git tag v1.0.0              # Create release tag
git push origin --tags       # Push tags to remote
```

## Report Builder Usage

### Creating Reports

1. Navigate to Admin Panel → Reporting → Saved Reports
2. Click "New Saved Report"
3. Configure:
   - Report type (advertiser performance, inventory, etc.)
   - Date range filters
   - Specific advertisers/campaigns/ad sizes
   - Grouping options
   - Metrics to include
4. Save the report configuration

### Running Reports

- Click "Run Report" to view results in browser
- Export to PDF for print-friendly format (DomPDF - lightweight PHP solution)
- Export to HTML for web-friendly format
- Export to Excel for data analysis

## Marketing Pages

### Home Page (`/`)

- Hero section with value proposition
- Feature highlights
- Customer testimonials
- Call-to-action sections

### Pricing Page (`/pricing`)

- Three-tier pricing structure
- Annual/monthly billing toggle
- Feature comparison table
- FAQ section

## Deployment

### Laravel Cloud (Recommended)

This project is optimized for Laravel Cloud deployment:

1. Connect your repository to Laravel Cloud
2. Configure environment variables in Laravel Cloud dashboard
3. Deploy automatically on git push

### Traditional Hosting

```bash
# Production build
npm run build
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Note: PDF generation uses DomPDF (pure PHP) - no external dependencies required
```

## Security Considerations

- All user input is validated through Form Requests
- CSRF protection enabled for all forms
- Proper authorization gates implemented
- Environment variables never committed to version control
- Secure file upload handling

## Performance Optimizations

- Database indexes on frequently queried columns
- Eager loading for relationships to prevent N+1 queries
- Frontend assets built and versioned with Vite
- Image optimization for marketing pages

## Development Workflow

### Git Workflow

This project follows trunk-based development with strict branch protection:

- **main**: Protected branch (never commit directly)
- **dev**: Integration branch for feature merges
- **feature/\***: Short-lived feature branches
- Pre-commit hooks prevent main branch commits
- Pre-push hooks require explicit consent

### Branch Naming

Use conventional branch names:

- `feature/multitenancy-reports`
- `fix/ou-scoping-issue`
- `docs/git-workflow`
- `refactor/report-queries`

### Task Management

We use Beads for atomic task tracking:

- Multitenancy tasks: `mt-001`, `mt-002`, etc.
- Git workflow tasks: `git-001`, `git-002`, etc.
- Documentation tasks: `docs-001`, `docs-002`, etc.

### Safety Rules

- **CRITICAL**: Never git push without explicit user consent
- Always ask for confirmation before any git push operation
- Use dry-run flags to preview push operations
- Verify remote branch before pushing

## Contributing

1. Follow PSR-12 coding standards
2. Write tests for new features
3. Use conventional commit messages
4. Keep pull requests focused and well-documented
5. Create feature branches from `dev`
6. Update Beads tasks as you progress
7. Never push to `main` directly

## License

This project is licensed under the MIT License.

## Documentation

- **[Git Workflow](GIT_WORKFLOW.md)**: Complete guide to our Git workflow
- **[AGENTS.md](AGENTS.md)**: Development guidelines and coding standards
- **[DEPLOYMENT.md](DEPLOYMENT.md)**: Deployment instructions
- **[LARAVEL_AD_OPS_PLAN.md](LARAVEL_AD_OPS_PLAN.md)**: Project planning document
- **[PDF Setup](PDF_SETUP.md)**: PDF export configuration and troubleshooting

## Support

For support and questions:

- Check the documentation files listed above
- Review existing issues in the repository
- Contact the development team
- Check Beads tasks for current development priorities

---

Built with ❤️ for media companies looking to streamline their advertising operations.
