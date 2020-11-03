# PHPStan Rules

[![Downloads](https://img.shields.io/packagist/dt/symplify/phpstan-rules.svg?style=flat-square)](https://packagist.org/packages/symplify/phpstan-rules/stats)

Set of rules for PHP_CodeSniffer, PHP-CS-Fixer and PHPStan used by Symplify projects.

**They run best with [EasyCodingStandard](https://github.com/symplify/easy-coding-standard)** and **PHPStan**.

## Install

```bash
composer require symplify/phpstan-rules --dev
composer require symplify/easy-coding-standard --dev
```

1. Run with [ECS](https://github.com/symplify/easy-coding-standard):

```bash
vendor/bin/ecs process src --set symplify
```

or even better

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

2. Register rules for PHPStan:

```yaml
# phpstan.neon
includes:
    - vendor/symplify/phpstan-rules/config/symplify-rules.neon
```

## Rules Overview

- [1. PHP_CodeSniffer sniffs](/docs/php_code_sniffer_sniffs.md)
- [2. PHP CS Fixer fixers](/docs/phpcs_fixer_fixers.md)
- [3. PHPStan rules](/docs/phpstan_rules.md)
    - [Object Calisthenics rules](/docs/phpstan_rules_object_calisthenics.md)
    - [Cognitive Complexity rules](/docs/phpstan_rules_cognitive_complexity.md)
    - ["No Arrays" rules](/docs/phpstan_rules_no_arrays.md)

<br>

## Contribute

The sources of this package are contained in the symplify monorepo. We welcome contributions for this package at [symplify/symplify](https://github.com/symplify/symplify).
