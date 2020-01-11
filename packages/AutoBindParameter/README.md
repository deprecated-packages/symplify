# Auto Bind Parameters for Symfony Apps

[![Build Status Github Actions](https://img.shields.io/github/workflow/status/Symplify/AutoBindParameter/Code_Checks?style=flat-square)](https://github.com/Symplify/AutoBindParameter/actions)
[![Downloads total](https://img.shields.io/packagist/dt/symplify/auto-bind-parameter.svg?style=flat-square)](https://packagist.org/packages/symplify/auto-bind-parameter/stats)

## Install

```bash
composer require symplify/auto-bind-parameter
```

## Usage

### 1. Register Compiler Pass in kernel

```php
<?php

namespace App;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\AutoBindParameter\DependencyInjection\CompilerPass\AutoBindParameterCompilerPass;

class AppKernel extends Kernel
{
    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new AutoBindParameterCompilerPass());
    }
}
```

### 2. Auto Bind Parameters

Keep your configs simple enough:

```diff
 parameters:
     entity_repository_class: 'Doctrine\ORM\EntityRepository'
     entity_manager_class: 'Doctrine\ORM\EntityManager'

 services:
-    _defaults:
-        bind:
-            $entityRepositoryClass: '%entity_repository_class%'
-            $entityManagerClass: '%entity_manager_class%'
-
     Rector\:
         resource: ..
```

And as any other bind:

```php
<?php

class SomeClass
{
    /**
     * @var string
     */
    private $entityRepositoryClass;

    public function __construct(string $entityRepositoryClass)
    {
        $this->entityRepositoryClass = $entityRepositoryClass;
    }
}
```
