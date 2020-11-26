# Skipper

Skip files by rule class, fnmatch or regex.

[![Downloads total](https://img.shields.io/packagist/dt/symplify/skipper.svg?style=flat-square)](https://packagist.org/packages/symplify/skipper/stats)

## Install

```bash
composer require symplify/skipper
```

Register bundle in your Kernel:

```php
namespace App;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\Skipper\Bundle\SkipperBundle;

final class AppKernel
{
    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [new SkipperBundle()];
    }
}
```

## Use

### 1. Configure with `Option::SKIP` parameter.

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\Skipper\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::SKIP, [
        // absolute directory
        __DIR__ . '/some-path',

        // absolute file
        __DIR__ . '/some-path/some-file.php',

        // with mask
        '*/Fixture/*',

        // specific class
        SomeClass::class,
    ]);
};
```

@todo

### 2. Configure with `Option::ONLY` parameter.

@todo

### 3. Use `Skipper` service in Your Project

You have 3 way to decide, if the *somethign* should be skipped:

- the element
- the file info
- the element in the file info

```php
use Symplify\Skipper\Skipper\Skipper;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SomeClass
{
    /**
     * @var Skipper
     */
    private $skipper;

    public function __construct(Skipper $skipper)
    {
        $this->skipper = $skipper;
    }

    /**
     * @param string|object $element
     */
    public function run($element, SmartFileInfo $fileInfo): void
    {
        // 1. skip the element?
        $shouldBeSkipped = $this->skipper->shouldSkipElement($element);

        // 2. skip the file path?
        $shouldBeSkipped = $this->skipper->shouldSkipFileInfo($fileInfo);

        // 3. skip the element in the file path?
        $shouldBeSkipped = $this->skipper->shouldSkipElementAndFileInfo($element, $fileInfo);
    }
}
```

<br>

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [Symplify monorepo issue tracker](https://github.com/symplify/symplify/issues)

## Contribute

The sources of this package are contained in the Symplify monorepo. We welcome contributions for this package on [symplify/symplify](https://github.com/symplify/symplify).
