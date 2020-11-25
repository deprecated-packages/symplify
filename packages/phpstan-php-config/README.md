# PHPStan PHP Config

[![Downloads](https://img.shields.io/packagist/dt/symplify/phpstan-php-config.svg?style=flat-square)](https://packagist.org/packages/symplify/phpstan-php-config/stats)

Use PHP config syntax to configure PHPStan in `phpstan.php`

---

Have you [switched from YAML to PHP configs](https://tomasvotruba.com/blog/2020/07/27/how-to-switch-from-yaml-xml-configs-to-php-today-with-symplify/) in Rector, ECS and Symfony projects? Do you still need [10 reasons why](https://tomasvotruba.com/blog/2020/07/16/10-cool-features-you-get-after-switching-from-yaml-to-php-configs/)?

In case you have, the `phpstan.neon` is probably the last one YAML-like config in your setup. Class renames are missed, so is autocomplete and prone to errors, since PHP-syntax is highly addictive.

**This package introduces `phpstan.php` syntax, so you can configure PHPStan in PHP.**

## Install

```bash
composer require symplify/phpstan-php-config --dev
```

## Usage

Create `phpstan.php` and configure it, as any other Symfony project with PHP config:

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanPHPConfig\ValueObject\Level;
use Symplify\PHPStanPHPConfig\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::LEVEL, Level::LEVEL_MAX);

    $parameters->set(Option::PATHS, [__DIR__ . '/packages']);

    $parameters->set(Option::PARALLEL_MAX_PROCESSES, 6);
    $parameters->set(Option::REPORT_UNMATCHED_IGNORED_ERRORS, false);
};
```

Then, add or extend "phpstan" scripts in your `composer.json`:

```json
{
    "scripts": {
        "phpstan": [
            "vendor/bin/phpstan-php-config convert phpstan.php --output-file phpstan-converted.neon",
            "vendor/bin/phpstan analyse --ansi --config phpstan-converter.neon"
        ]
    }
}
```

Then run PHPStan as usual:

```bash
composer phpstan
```

If everything works well, you can drop the custom config path and `phpstan.neon` will be generated instead:

```json
{
    "scripts": {
        "phpstan": [
            "vendor/bin/phpstan-php-config convert phpstan.php",
            "vendor/bin/phpstan analyse --ansi"
        ]
    }
}
```

<br>

## Contribute

The sources of this package are contained in the symplify monorepo. We welcome contributions for this package at [symplify/symplify](https://github.com/symplify/symplify).
