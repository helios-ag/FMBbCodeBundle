# Upgrade Guide: FMBbCodeBundle v7 → v8

This document outlines the steps required to upgrade from FMBbCodeBundle v7 (Symfony 3.x) to v8 (Symfony 6.4+/7.x).

## Breaking Changes

### 1. Minimum Requirements
- **PHP**: Upgraded from 5.5+ to **8.1+**
- **Symfony**: Upgraded from 3.x to **6.4+ or 7.x**

### 2. Removed Dependencies
The following dependencies have been removed:
- `symfony/assetic-bundle` (deprecated)
- `symfony/templating` (deprecated in Symfony 4+)
- `sensio/distribution-bundle` (deprecated)

### 3. Service Configuration Changes
Services are now properly configured for modern Symfony versions:
- Updated XML schema URLs to HTTPS
- Improved service definitions
- Container interface usage updated

## Migration Steps

### 1. Update PHP Version
Ensure your project runs on **PHP 8.1 or higher**.

### 2. Update Composer Dependencies

```bash
composer require helios-ag/fm-bbcode-bundle:^8.0
```

### 3. Update Your Bundle Registration
No changes required - bundle registration remains the same:

```php
// config/bundles.php (Symfony 4+)
return [
    // ... other bundles
    FM\BbcodeBundle\FMBbcodeBundle::class => ['all' => true],
];

// OR app/AppKernel.php (legacy)
$bundles = [
    // ... other bundles
    new FM\BbcodeBundle\FMBbcodeBundle(),
];
```

### 4. Configuration Updates
Your existing configuration should continue to work. Example:

```yaml
# config/packages/fm_bbcode.yaml
fm_bbcode:
  filter_sets:
    my_default_filter:
      locale: en
      xhtml: true
      filters: [default]
      hooks: [emoticon]
```

### 5. Template Usage
Twig filters remain unchanged:

```twig
{{ '[b]Bold text[/b]'|bbcode_filter('my_default_filter') }}
{{ '[b]Text with BBCode[/b]'|bbcode_clean }}
```

## Testing Your Upgrade

1. **Clear Cache**: `php bin/console cache:clear`
2. **Test BBCode Rendering**: Verify that BBCode filters work in your templates
3. **Test Emoticons**: If using emoticons, test the emoticon functionality
4. **Run Tests**: Execute your test suite to ensure everything works

## Potential Issues & Solutions

### Issue: Service Container Errors
If you encounter service container errors, ensure you're running on Symfony 6.4+ or 7.x.

### Issue: Twig Extension Not Found
Clear your cache completely:
```bash
rm -rf var/cache/*
php bin/console cache:clear
```

### Issue: Emoticon Images Not Loading
Re-run the emoticon dump command:
```bash
php bin/console bbcode:dump
```

## Rollback Instructions

If you need to rollback:

1. Revert to the previous bundle version:
   ```bash
   composer require helios-ag/fm-bbcode-bundle:^7.0
   ```

2. Clear cache:
   ```bash
   php bin/console cache:clear
   ```

## Support

For issues or questions about the upgrade:
- Check the [GitHub Issues](https://github.com/helios-ag/FMBbCodeBundle/issues)
- Review the [CHANGELOG.md](CHANGELOG.md) for detailed changes
