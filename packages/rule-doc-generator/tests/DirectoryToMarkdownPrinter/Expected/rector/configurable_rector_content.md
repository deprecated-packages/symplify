# Rules Overview

## ConfigurableRector

Some change

:wrench: **configure it!**

- class: `Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Fixture\ConfigurableRector\ConfigurableRector`

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Fixture\ConfigurableRector\ConfigurableRector;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(ConfigurableRector::class)
        ->call('configure', [['key' => 'value']]);
};
```

↓

```diff
-before
+after
```

<br>
