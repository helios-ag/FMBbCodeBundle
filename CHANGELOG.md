## 8.0.0 (2024-12-XX)

**BREAKING CHANGES - Symfony 6.4+ / 7.x Compatibility Update**

### Major Changes
* **PHP 8.1+ required** - Dropped support for PHP < 8.1
* **Symfony 6.4+ / 7.x support** - Updated all dependencies to latest Symfony versions
* **Updated Twig integration** - Migrated from deprecated `\Twig_Extension` to `AbstractExtension`
* **Modern service configuration** - Updated XML service definitions to use current schemas
* **PHPUnit 10 support** - Updated test configuration for modern PHPUnit

### Technical Updates
* Replaced `\Twig_Extension` with `Twig\Extension\AbstractExtension`
* Replaced `\Twig_SimpleFilter` with `TwigFilter`
* Replaced `\Twig_Error_Runtime` with `Twig\Error\RuntimeError`
* Updated `TreeBuilder` API usage in configuration
* Updated service container usage for Symfony 6.4+/7.x compatibility
* Updated XML service schemas to HTTPS endpoints
* Removed deprecated dependencies (assetic-bundle, sensio/distribution-bundle)

### Removed Features
* Dropped support for Symfony < 6.4
* Removed templating component dependency (deprecated in Symfony)

---

## Previous Versions

1.02.2013
* Now can accept short and long locale codes

30.01.2013
* Upgraded to Decoda 5.x
* Added clean filter (bbcode_clean, to strip-out tags)
* Emoticons Dump Command

14.03.2012:
* Root node for a configuration now called fm_bbcode
* Changed a lot of files, adda php template helper
* Changed behavior to filters function like ( in avalanche123/liip imagine bundle), i.e. from simple BBCode
  twig extension to a little bit more complicated bbcode_filter('my_filter')
* Multiple bbcode filters now available