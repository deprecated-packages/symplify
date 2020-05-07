# PHPStan Extensions

[![Downloads total](https://img.shields.io/packagist/dt/symplify/phpstan-extensions.svg?style=flat-square)](https://packagist.org/packages/symplify/phpstan-extensions/stats)

## Install

```yaml
# phpstan.neon
includes:
    - 'vendor/symplify/phpstan-extensions/config/config.neon'
```

## Use

### Symplify Error Formatter

*Works best with [anthraxx/intellij-awesome-console](https://github.com/anthraxx/intellij-awesome-console)*

- Do you want to **click the error and get right to the line in the file** it's reported at?
- Do you want to **copy-paste regex escaped error to your `ignoreErrors`**?

```bash
vendor/bin/phpstan analyse src --level max --error-format symplify
```

↓

```bash
------------------------------------------------------------------------------------------
src/Command/ReleaseCommand.php:51
------------------------------------------------------------------------------------------
- "Call to an undefined method Symplify\\Command\\ReleaseCommand\:\:nonExistingCall\(\)"
------------------------------------------------------------------------------------------
```

The config also loads few return type extensions.

### Return Type Extensions

#### `Symplify\PHPStanExtensions\Symfony\Type\ContainerGetTypeExtension`

With Symfony container and type as an argument, you always know **the same type is returned**:

```php
<?php

use Symfony\Component\DependencyInjection\Container;

/** @var Container $container */
$container->get(Type::class); // PHPStan: object ❌
$container->get(Type::class); // Reality: Type ✅

// same for in-controller/container-aware context
$this->get(Type::class);
```

#### `Symplify\PHPStanExtensions\Symfony\Type\KernelGetContainerAfterBootReturnTypeExtension`

After Symfony Kernel boot, `getContainer()` always returns the container:

```php
<?php

use Symfony\Component\HttpKernel\Kernel;

final class AppKernel extends Kernel
{
    // ...
}

$kernel = new AppKernel('prod', false);
$kernel->boot();

$kernel->getContainer(); // PHPStan: null|ContainerInterface ❌
$kernel->getContainer(); // Reality: ContainerInterface ✅
```

#### `Symplify\PHPStanExtensions\Symfony\Type\SplFileInfoTolerantDynamicMethodReturnTypeExtension`

Symfony Finder finds only existing files (obviously), so the `getRealPath()` always return `string`:

```php
<?php

use Symfony\Component\Finder\Finder;

$finder = new Finder;

foreach ($finder as $fileInfo) {
    $fileInfo->getRealPath(); // PHPStan: false|string ❌
    $fileInfo->getRealPath(); // Reality: string ✅
}
```
