# Coding Standard

[![Downloads](https://img.shields.io/packagist/dt/symplify/coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/coding-standard/stats)

Set of rules for PHP_CodeSniffer, PHP-CS-Fixer and PHPStan used by Symplify projects.

**They run best with [EasyCodingStandard](https://github.com/symplify/easy-coding-standard)** and **PHPStan**.

## Install

```bash
composer require symplify/coding-standard --dev
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
    - vendor/symplify/coding-standard/config/symplify-rules.neon
```

## Rules Overview

- [1. PHP_CodeSniffer sniffs](/docs/php_code_sniffer_sniffs.md)
- [2. PHP CS Fixer fixers](/docs/phpcs_fixer_fixers.md)
- [3. PHPStan rules](/docs/phpstan_rules.md)

Particular PHPStan sub-sets:

- [Object Calisthenics rules](/docs/phpstan_rules.md#object-calisthenics-rules)
- [Cognitive Complexity rules](/docs/phpstan_rules.md#cognitive-complexity)

<br>

## Contributing

Open an [issue](https://github.com/symplify/symplify/issues) or send a [pull-request](https://github.com/symplify/symplify/pulls) to main repository.
