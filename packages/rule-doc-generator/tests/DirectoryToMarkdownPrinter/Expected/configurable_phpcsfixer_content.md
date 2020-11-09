# Rules Overview

## SomeConfiguredFixer

Some description

:wrench: **configure it!**

- class: `Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Fixture\ConfigurablePHPCSFixer\SomeConfiguredFixer`

```diff
-bad code
+good code
```

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Fixture\ConfigurablePHPCSFixer\SomeConfiguredFixer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(SomeConfiguredFixer::class)
        ->call('configure', [['key' => 'value']]);
};
```

<br>
