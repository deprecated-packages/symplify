# Console Package Builder

[![Downloads total](https://img.shields.io/packagist/dt/symplify/console-package-builder.svg?style=flat-square)](https://packagist.org/packages/symplify/console-package-builder/stats)

Package to speed up building command line applications

## Install

```bash
composer require symplify/console-package-builder --dev
```

## Namesless Commands

Do you want to have convention in command naming? Read [The Bullet Proof Symfony Command Naming](https://tomasvotruba.com/blog/2020/10/26/the-bullet-proof-symfony-command-naming/)

```php
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\ConsolePackageBuilder\DependencyInjection\CompilerPass\NamelessConsoleCommandCompilerPass;

class SomeKernel extends Kernel implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addCompilerPass(new NamelessConsoleCommandCompilerPass());
    }
}
```

<br>

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [Symplify monorepo issue tracker](https://github.com/symplify/symplify/issues)

## Contribute

The sources of this package are contained in the Symplify monorepo. We welcome contributions for this package on [symplify/symplify](https://github.com/symplify/symplify).
