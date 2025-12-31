# Release Process

This document describes how to create a new release of hell2shape.

## Automated Release Workflow

The project uses GitHub Actions to automatically manage version numbers. When you push a new git tag, the workflow will:

1. Extract the version number from the tag
2. Generate `src/Version.php` with the version and release date
3. Commit the updated file back to the repository

## Creating a New Release

### 1. Ensure Everything is Ready

```bash
# Make sure all changes are committed
git status

# Run tests to ensure everything works
vendor/bin/phpunit

# Run static analysis
vendor/bin/phpstan analyse

# Check code style
vendor/bin/ecs check
```

### 2. Create and Push a Git Tag

```bash
# Create a new tag (e.g., for version 0.2.0)
git tag -a v0.2.0 -m "Release version 0.2.0"

# Push the tag to GitHub
git push origin v0.2.0
```

### 3. GitHub Actions Takes Over

The workflow (`.github/workflows/release.yml`) will automatically:
- Generate `src/Version.php` with version `0.2.0`
- Commit the file back to the main branch

### 4. Packagist Updates Automatically

If you've set up the Packagist webhook (recommended), the new version will be available on Packagist within minutes.

If not, you can manually trigger an update on your package's Packagist page.

## Version Numbering

Follow [Semantic Versioning](https://semver.org/):

- **MAJOR** version (1.0.0): Incompatible API changes
- **MINOR** version (0.2.0): New functionality, backwards compatible
- **PATCH** version (0.1.1): Bug fixes, backwards compatible

## Version.php

The `src/Version.php` file is automatically generated and should not be edited manually. It contains:

```php
final class Version
{
    public const VERSION = '0.2.0';
    public const RELEASE_DATE = '2025-12-31';
}
```

This file is used by the CLI application to display the version number:

```bash
bin/hell2shape --version
# Output: hell2shape 0.2.0
```

## Troubleshooting

### Workflow Fails

If the GitHub Actions workflow fails:

1. Check the workflow logs in the "Actions" tab on GitHub
2. Ensure you have the correct permissions set in the workflow file
3. Verify the tag format is correct (must start with `v`)

### Version Not Updated

If the version doesn't update after pushing a tag:

1. Check that the workflow ran successfully
2. Pull the latest changes: `git pull origin main`
3. Verify `src/Version.php` was updated

### Packagist Not Updating

If Packagist doesn't show the new version:

1. Check if the webhook is configured correctly
2. Manually trigger an update on Packagist
3. Wait a few minutes - updates can take time
