# Set Config Resolver

[![Build Status](https://img.shields.io/travis/Symplify/SetConfigResolver/master.svg?style=flat-square)](https://travis-ci.org/Symplify/SetConfigResolver)
[![Downloads total](https://img.shields.io/packagist/dt/symplify/set-config-resolver.svg?style=flat-square)](https://packagist.org/packages/symplify/set-config-resolver/stats)

## Install

```bash
composer require symplify/set-config-resolver
```

## Use

### A. Set From Root Config

...

### B. Set From `--set` Option in CLI

...

### C. Config From `--config` Option in CLI

...

### Load a Config for CLI Application?

- Read [How to Load --config With Services in Symfony Console](https://www.tomasvotruba.cz/blog/2018/05/14/how-to-load-config-with-services-in-symfony-console/#code-argvinput-code-to-the-rescue)

Use in CLI entry file `bin/<app-name>`, e.g. `bin/ecs` or `bin/rector`.

```php
<?php

# bin/ecs

declare(strict_types=1);

use Symfony\Component\Console\Input\ArgvInput;
use Symplify\SetConfigResolver\ConfigResolver;

$configs = [];

// 1. --config CLI option or local fallback
$configResolver = new ConfigResolver();
$inputConfig = $configResolver->resolveFromInputWithFallback(new ArgvInput(), [
    'ecs.yml', 'ecs.yaml', 'easy-coding-standard.yml', 'easy-coding-standard.yaml',
]);

if ($inputConfig !== null) {
    $configs[] = $inputConfig;
}

// 2. --set CLI option
$setInputConfig = $configResolver->resolveSetFromInputAndDirectory(new ArgvInput(), __DIR__ . '/../config');
if ($setInputConfig) {
    $configs[] = $setInputConfig;
}

// 3. "parameters > set" in provided yaml files
$setsDirectory = __DIR__ . '/../config/sets';
$parameterSetsConfigs = $configResolver->resolveFromParameterSetsFromConfigFiles($configs, $setsDirectory);
if ($parameterSetsConfigs !== []) {
    $configs = array_merge($configs, $parameterSetsConfigs);
}

// Build DI container
$appKernel = new AppKernel('prod', true);
if ($configs !== []) {
    $appKernel->setConfigs($configs);
}

$appKernel->boot();

$container = $appKernel->getContainer();
```

And use like this:

```bash
vendor/bin/your-app --config config/set/the-config.yaml
```

...or...

```bash
vendor/bin/your-app --set the-config
```

...or with this config:

```bash
parameters:
    sets:
        - "the-config"
```

All are equal :)