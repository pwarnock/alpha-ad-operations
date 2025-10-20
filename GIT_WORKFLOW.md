# Git Workflow Documentation

## Overview

This project follows a trunk-based development workflow with strict branch protection and safety measures to ensure code quality and prevent accidental pushes.

## Branch Strategy

### Main Branches

- **`main`**: Protected branch, never commit directly
  - Only accepts merges from `dev` via pull requests
  - Represents production-ready code
  - Tags are created from `main` for releases

- **`dev`**: Integration branch
  - All feature branches merge into `dev`
  - Represents next release candidate
  - Continuous integration runs on this branch

### Feature Branches

Format: `type/description`

**Types:**

- `feature/` - New features
- `fix/` - Bug fixes
- `hotfix/` - Critical production fixes
- `chore/` - Maintenance tasks
- `docs/` - Documentation updates
- `test/` - Test improvements
- `refactor/` - Code refactoring

**Examples:**

- `feature/multitenancy-reports`
- `fix/ou-scoping-issue`
- `docs/git-workflow`
- `refactor/report-queries`

## Workflow Process

### 1. Start New Work

```bash
# Ensure you're on latest dev
git checkout dev
git pull origin dev

# Create feature branch
git checkout -b feature/your-feature-name
```

### 2. Development Work

- Make atomic commits with conventional commit messages
- Pre-commit hooks will validate branch naming and commit messages
- Run tests and linting locally

### 3. Complete Feature

```bash
# Push feature branch (requires explicit consent)
git push origin feature/your-feature-name

# Create pull request to dev branch
# Wait for code review and CI checks
```

### 4. Merge and Release

```bash
# Merge to dev after approval
git checkout dev
git merge feature/your-feature-name
git push origin dev

# When ready for release:
git checkout main
git merge dev
git tag v1.0.0
git push origin main --tags
```

## Safety Measures

### Pre-commit Hooks

- **Main Branch Protection**: Prevents direct commits to `main`
- **Branch Naming**: Enforces conventional branch naming
- **Commit Message Format**: Encourages conventional commits
- **Basic Linting**: Runs available linting tools

### Pre-push Hooks

- **Explicit Consent**: Requires confirmation before pushing
- **Main Protection**: Blocks pushes to `main` branch
- **Safety Confirmation**: Double-checks push intentions

### Git Push Safety Rule

**CRITICAL**: Never git push without explicit user consent

- Always ask for confirmation before any git push operation
- Use dry-run flags to preview push operations
- Verify remote branch before pushing

## Commit Message Format

Follow conventional commits specification:

```
type(scope): description

[optional body]

[optional footer(s)]
```

**Types:**

- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation
- `style`: Formatting, missing semi colons, etc.
- `refactor`: Code refactoring
- `test`: Adding missing tests
- `chore`: Maintenance tasks

**Examples:**

```
feat(reports): add multitenancy support for saved reports

- Add tenant_id and organization_id columns
- Update model relationships
- Implement OU-based scoping

Closes #123
```

## Tagging Strategy

Use semantic versioning tags:

- **v1.0.0**: Major release (breaking changes)
- **v1.1.0**: Minor release (new features)
- **v1.1.1**: Patch release (bug fixes)

### Tag Creation

```bash
# Create annotated tag
git tag v1.0.0 -m "Release v1.0.0: Add multitenancy support"

# Push tags
git push origin --tags
```

### Quick Reset with Tags

```bash
# Reset to specific tag
git checkout v1.0.0
git checkout -b hotfix/emergency-fix

# After fix:
git checkout main
git merge hotfix/emergency-fix
git tag v1.0.1
```

## Beads Integration

This project uses Beads for atomic task tracking:

### Task Management

```bash
# View available tasks
beads list

# See ready tasks
beads ready

# Show task details
beads show mt-001

# Update task status
beads update mt-001 --status in_progress
```

### Task Naming Convention

- `mt-###`: Multitenancy tasks
- `git-###`: Git workflow tasks
- `docs-###`: Documentation tasks
- `safety-###`: Safety and security tasks

## Emergency Procedures

### Hotfix Process

1. Create `hotfix/` branch from `main`
2. Fix the issue
3. Create pull request to `main` (bypassing `dev`)
4. Merge and tag new patch version
5. Merge hotfix back to `dev`

### Rollback Process

```bash
# Reset to previous tag
git checkout v1.0.0
git checkout -b rollback/v1.0.1

# Force push rollback (EXTREME CAUTION)
git push origin main --force-with-lease
```

## Best Practices

1. **Small, Atomic Commits**: Each commit should do one thing
2. **Feature Branches**: Keep branches short-lived
3. **Regular Merges**: Merge to `dev` frequently to reduce conflicts
4. **Tag Releases**: Always tag releases for easy rollback
5. **Documentation**: Update docs with significant changes
6. **Testing**: Ensure all tests pass before merging
7. **Code Review**: All changes require review before merging

## Troubleshooting

### Pre-commit Hook Issues

```bash
# Bypass hooks (emergency only)
git commit --no-verify -m "emergency fix"
```

### Push Permission Denied

```bash
# Check current branch
git branch

# Switch to correct branch
git checkout feature/your-branch
```

### Merge Conflicts

```bash
# Resolve conflicts in dev branch
git checkout dev
git pull origin dev
git merge feature/your-branch
# Resolve conflicts, then:
git add .
git commit -m "resolve merge conflicts"
```
