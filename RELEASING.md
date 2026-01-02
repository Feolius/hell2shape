# Release Process

This document describes how to create a new release of hell2shape.

## Manual Release Workflow

The project uses GitHub Actions with manual workflow dispatch to manage version numbers. This gives you full control over when releases are created.

## Creating a New Release

### 1. Ensure Everything is Ready

```bash
# Make sure all changes are committed and pushed
git status
git push origin main

# Run tests to ensure everything works
vendor/bin/phpunit

# Run static analysis
vendor/bin/phpstan analyse

# Check code style
vendor/bin/ecs check
```

### 2. Trigger the Release Workflow on GitHub

1. Go to your repository on GitHub
2. Click on the **"Actions"** tab
3. Select **"Release"** workflow from the left sidebar
4. Click **"Run workflow"** button (top right)
5. Fill in the required field:
   - **Version number**: Enter the version (e.g., `0.2.0` - **without** the `v` prefix)
6. Click **"Run workflow"** to start

### 3. GitHub Actions Takes Over

The workflow (`.github/workflows/release.yml`) will automatically:
- Generate `src/Version.php` with the specified version and current date
- Commit the file to the main branch with message "Release vX.Y.Z"
- Create and push a git tag `vX.Y.Z`

### 4. Create a GitHub Release (Optional but Recommended)

After the workflow completes:

1. Go to your repository on GitHub
2. Click on **"Releases"** in the right sidebar (or go to `/releases`)
3. Click **"Draft a new release"**
4. Select the tag that was just created (e.g., `v0.2.0`)
5. Add a release title (e.g., "Version 0.2.0")
6. Write release notes describing:
   - New features
   - Bug fixes
   - Breaking changes
   - Any other relevant information
7. Click **"Publish release"**

This creates a formal release on GitHub with your detailed release notes, making it easier for users to understand what changed.

### 5. Packagist Updates Automatically

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
3. Verify you entered the version number correctly (without `v` prefix)
4. Make sure the main branch is up to date

### Version Not Updated

If the version doesn't update after running the workflow:

1. Check that the workflow ran successfully in the Actions tab
2. Pull the latest changes: `git pull origin main`
3. Verify `src/Version.php` was updated
4. Check that the git tag was created: `git fetch --tags && git tag -l`

### Packagist Not Updating

If Packagist doesn't show the new version:

1. Check if the webhook is configured correctly
2. Manually trigger an update on Packagist
3. Wait a few minutes - updates can take time
