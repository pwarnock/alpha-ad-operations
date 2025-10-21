# Lead Developer Coordination Questions

## Architecture & Structure Questions

1. **Package Integration**: Should we register the `/packages/reports/` plugin in the main application's service providers, or keep it as a standalone distributable package?

2. **Multi-Panel Strategy**: We have Publisher panel working. Should we also implement the Backoffice panel, or focus solely on the Publisher functionality?

3. **Test Namespace Fix**: Feature tests are looking for resources in `\App\Filament\Resources\` but they're in `\App\Filament\Publisher\Resources\`. Should I update all test namespaces or restructure the resources?

## Development Priorities

4. **Phase 2 Focus**: What should be the immediate priority for the reports package - core reporting engine, UI components, or data processing pipelines?

5. **Database Schema**: Are the current multitenancy columns (`tenant_id`, `organization_id`, `organizational_unit_id`) sufficient for the reporting requirements?

6. **Performance Requirements**: Any specific performance benchmarks or data volume expectations we should design for?

## Technical Decisions

7. **Reporting Engine**: Should we use the existing `eloquent-power-joins` dependency in the reports package, or implement a different query optimization strategy?

8. **Export Formats**: The current setup supports PDF/Excel via `maatwebsite/excel` and `spatie/laravel-pdf`. Are additional formats needed (CSV, JSON, etc.)?

9. **Caching Strategy**: What caching approach should we implement for report generation - Redis, file-based, or database?

## Workflow & Process

10. **Git Workflow**: Should I continue working on the `feature/analytics-proposal` branch, or create new branches for each component?

11. **Testing Strategy**: Should I fix the existing failing feature tests first, or focus on new development and circle back to tests?

12. **Documentation**: What level of documentation is needed - API docs, user guides, developer setup instructions?

## Integration & Dependencies

13. **SaaSykit Compatibility**: The reports package includes `SaaSykitCompatible.php` contract. Should I implement full SaaSykit integration or keep it optional?

14. **External APIs**: Are there any third-party advertising platforms or data sources we need to integrate with?

15. **Authentication**: Should reports use the existing Laravel Sanctum token system, or implement separate authentication for report access?

## Deployment & Environment

16. **Environment Setup**: Any specific requirements for local development - Docker, specific PHP versions, database configurations?

17. **Asset Building**: Should I set up Vite build process for the reports package frontend assets?

18. **Database Migrations**: Should the reports package include its own migrations, or integrate with the main application's migration system?

## Timeline & Milestones

19. **MVP Definition**: What constitutes the minimum viable product for the reports functionality?

20. **Next Sprint Goals**: What specific deliverables should I focus on for the next development cycle?

## Code Quality & Standards

21. **Code Style**: Any specific coding standards or tools beyond Laravel conventions I should follow?

22. **Error Handling**: Should I implement specific error handling patterns for the reporting engine?

23. **Logging Requirements**: Any specific logging or monitoring requirements for report generation and usage?

---

## Current Project Status

### ✅ Completed

- Fixed autoloader path for reports package (`packages/alpha/reports/` → `packages/reports/`)
- Restored missing `SavedReportResource.php` to Publisher panel
- Added 301 redirect from `/admin` to `/publisher` to resolve route conflicts
- Recreated synthetic test data (15 advertisers, 18 campaigns, 32 line items, 957 impressions, 10 saved reports)
- Fixed database schema with missing `tenant_id` columns for campaigns and line_items
- Created missing factories for Tenant, Organization, OrganizationalUnit models
- Fixed User model unit test with updated fillable attributes
- All unit tests now passing (20/20)

### ⚠️ Current Issues

- Feature tests failing due to namespace mismatch (looking for resources in wrong Filament panel)
- Reports package not yet registered in main application

### 📁 Package Structure

- `/packages/reports/` - Main reports package with proper PSR-4 autoloading
- Includes Filament resources, services, models, and SaaSykit integration
- Dependencies: eloquent-power-joins, Laravel PDF/Excel export capabilities

### 🧪 Test Suite Status

- **Unit Tests**: 20/20 passing ✅
- **Feature Tests**: Multiple failures due to namespace issues ❌
- Test coverage includes: Models, Filament resources, API auth, Authorization, Report management

---

_Last updated: 2025-10-20_
