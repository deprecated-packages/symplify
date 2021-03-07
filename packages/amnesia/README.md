# Amnesia - Relief for your long-term memory

[![Downloads total](https://img.shields.io/packagist/dt/symplify/amnesia.svg?style=flat-square)](https://packagist.org/packages/symplify/amnesia/stats)

## Install

```bash
composer require symplify/amnesia
```

## Usage

This package helps with [Symfony PHP configs](https://tomasvotruba.com/blog/2020/07/27/how-to-switch-from-yaml-xml-configs-to-php-today-with-migrify/).

Update typo prone stringy configs to realiable PHP constants:

### `FrameworkExtension`

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symplify\Amnesia\Functions\env;
use Symplify\Amnesia\ValueObject\Symfony\Extension\FrameworkExtension;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(FrameworkExtension::NAME, [
        FrameworkExtension::SECRET => env('APP_SECRET'),
    ]);
};
```

### `TwigExtension`

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\Amnesia\ValueObject\Symfony\Extension\Twig\NumberFormat;
use Symplify\Amnesia\ValueObject\Symfony\Extension\TwigExtension;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(TwigExtension::NAME, [
        TwigExtension::DEFAULT_PATH => '%kernel.project_dir%/templates',
        TwigExtension::PATHS => [
            __DIR__ . '/../../packages/framework-stats/templates',
        ],
        TwigExtension::GLOBALS => [
            'site_title' => 'Tomas Votruba',
        ],
        // see https://symfony.com/blog/new-in-symfony-2-7-default-date-and-number-format-configuration
        TwigExtension::NUMBER_FORMAT => [
            NumberFormat::DECIMALS => 0,
            NumberFormat::DECIMAL_POINT => '.',
            NumberFormat::THOUSANDS_SEPARATOR => ' ',
        ],
    ]);
};
```

### `Routing`

```php
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symplify\Amnesia\ValueObject\Symfony\Routing;

return static function (RoutingConfigurator $routes): void {
    $routes->import(__DIR__ . '/../src/Controller', Routing::TYPE_ANNOTATION);
};
```

### `DoctrineExtension`

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symplify\Amnesia\Functions\env;
use Symplify\Amnesia\ValueObject\Symfony\Extension\Doctrine\DBAL;
use Symplify\Amnesia\ValueObject\Symfony\Extension\Doctrine\Mapping;
use Symplify\Amnesia\ValueObject\Symfony\Extension\Doctrine\ORM;
use Symplify\Amnesia\ValueObject\Symfony\Extension\DoctrineExtension;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(DoctrineExtension::NAME, [
        DoctrineExtension::DBAL => [
            DBAL::HOST => env('DATABASE_HOST'),
            DBAL::DBNAME => env('DATABASE_DBNAME'),
            DBAL::USER => env('DATABASE_USER'),
            DBAL::PASSWORD => env('DATABASE_PASSWORD'),
        ],
        DoctrineExtension::ORM => [
            ORM::AUTO_GENERATE_PROXY_CLASSES => true,
            ORM::MAPPINGS => [
                'demo' => [
                    Mapping::IS_BUNDLE => false,
                    Mapping::DIR => __DIR__ . '/../../packages/demo/src/Entity',
                    Mapping::PREFIX => 'Rector\Website\Demo\Entity',
                ],
            ],
        ],
    ]);
};
```

<br>

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [Symplify monorepo issue tracker](https://github.com/symplify/symplify/issues)

## Contribute

The sources of this package are contained in the Symplify monorepo. We welcome contributions for this package on [symplify/symplify](https://github.com/symplify/symplify).
