# Easy CI

[![Downloads total](https://img.shields.io/packagist/dt/symplify/easy-ci.svg?style=flat-square)](https://packagist.org/packages/symplify/easy-ci/stats)

Tools that make easy to setup CI.

## Install

```bash
composer require symplify/easy-ci --dev
```

## Usage

### 1. Check your Code for Git Merge Conflicts

Do you use Git? Then merge conflicts is not what you want in your code ever to see:

```bash
<<<<<<< HEAD
this is some content to mess with
content to append
=======
totally different content to merge later
````

How to avoid it? Add check to your CI:

```bash
vendor/bin/easy-ci check-conflicts .
```

The `/vendor` directory is excluded by default.

### 2. Generate Sonar Cube config file `sonar-project.properties`

This command comes very handy, **if you change, add or remove your paths to your PHP code**. While not very common, it comes handy in monorepo or local packages. No need to update `sonar-project.properties` manually - this command automates it!

First, read [how to enable Sonar Cube for your project](https://tomasvotruba.com/blog/2020/02/24/how-many-days-of-technical-debt-has-your-php-project/).

Then create `easy-ci.php` with following values:

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCI\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::SONAR_ORGANIZATION, 'symplify');
    $parameters->set(Option::SONAR_PROJECT_KEY, 'symplify_symplify');
    // paths to your source, packages and tests
    $parameters->set(Option::SONAR_DIRECTORIES, [__DIR__ . '/src', __DIR__ . '/tests', __DIR__ . '/packages']);

    // optional - for extra parameters
    $parameters->set(Option::SONAR_OTHER_PARAMETERS, [
        'sonar.extra' => 'extra_values',
    ]);
};
```

Last, generate the file:

```bash
vendor/bin/easy-ci generate-sonar
```

That's it!

<br>

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [Symplify monorepo issue tracker](https://github.com/symplify/symplify/issues)

## Contribute

The sources of this package are contained in the Symplify monorepo. We welcome contributions for this package on [symplify/symplify](https://github.com/symplify/symplify).
