# Coding Standard

[![Downloads](https://img.shields.io/packagist/dt/symplify/coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/coding-standard/stats)

Set of rules for PHP_CodeSniffer and PHP-CS-Fixer used by Symplify projects.

**They run best with [EasyCodingStandard](https://github.com/symplify/easy-coding-standard)** and **PHPStan**.

## Install

```bash
composer require symplify/coding-standard --dev
composer require symplify/easy-coding-standard --dev
```

1. Run with [ECS](https://github.com/symplify/easy-coding-standard):

```diff
# ecs.php
 <?php

 declare(strict_types=1);

 use Symplify\EasyCodingStandard\ValueObject\Option;
+use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

 return static function (ContainerConfigurator $containerConfigurator): void {
     $parameters = $containerConfigurator->parameters();

+    $parameters->set(Option::SETS, [
+        SetList::SYMPLIFY,
+    ]);
 };
```

## Rules Overview

- [Rules Overview](/docs/rules_overview.md)

<br>

## Contribute

The sources of this package are contained in the symplify monorepo. We welcome contributions for this package at [symplify/symplify](https://github.com/symplify/symplify).
