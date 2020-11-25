# 1 Rules Overview

## SomeConfiguredFixer

Some description

:wrench: **configure it!**

- class: `Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Fixture\PHPCSFixer\Configurable\SomeConfiguredFixer`

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Fixture\PHPCSFixer\Configurable\SomeConfiguredFixer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(SomeConfiguredFixer::class)
        ->call('configure', [['key' => 'value']]);
};
```

↓

```diff
-bad code
+good code
```

<br>
