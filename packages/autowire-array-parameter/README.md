# Autowire Array Parameters for Symfony Apps

[![Downloads total](https://img.shields.io/packagist/dt/symplify/autowire-array-parameter.svg?style=flat-square)](https://packagist.org/packages/symplify/autowire-array-parameter/stats)

## Install

```bash
composer require symplify/autowire-array-parameter
```

## Usage

### 1. Register Compiler Pass in kernel

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;

class AppKernel extends Kernel
{
    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(
            new AutowireArrayParameterCompilerPass(
                [
                    // place for excluding types to resolve edge cases
                    'Sonata\CoreBundle\Model\Adapter\AdapterInterface',
                ]
            )
        );
    }
}
```

### 2. Autowire Array Parameters

This feature surpasses YAML-defined, tag-based or CompilerPass-based collectors in minimalistic way:

```php
<?php

declare(strict_types=1);

class Application
{
    /**
     * @var Command[]
     */
    private $commands = [];

    /**
     * @param Command[] $commands
     */
    public function __construct(array $commands)
    {
        $this->commands = $commands;
        // instance of Command collected from all services
        var_dump($commands);
    }
}
```

## Contribute

The sources of this package are contained in the symplify monorepo. We welcome contributions for this package at [symplify/symplify](https://github.com/symplify/symplify).
