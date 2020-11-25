# Static Detector

[![Downloads total](https://img.shields.io/packagist/dt/symplify/static-detector.svg?style=flat-square)](https://packagist.org/packages/symplify/static-detector/stats)

Detect static and its calls in your project!

## Install

```bash
composer require symplify/static-detector --dev
```

## Usage

```bash
vendor/bin/static-detector detect src
```

## Configuration

Do you want to look only on specific classes? Just create `static-detector.php` config in your root and add filter them:

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\StaticDetector\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::FILTER_CLASSES, ['*\\Helpers']);
};
```

That's it :)

<br>

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [Symplify monorepo issue tracker](https://github.com/symplify/symplify/issues)

## Contribute

The sources of this package are contained in the Symplify monorepo. We welcome contributions for this package on [symplify/symplify](https://github.com/symplify/symplify).
