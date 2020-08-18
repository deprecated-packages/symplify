# PHP Code Sniffer Sniffs

## There should not be comments with valid code

- class: [`Symplify\CodingStandard\Sniffs\Debug\CommentedOutCodeSniff`](../src/Sniffs/Debug/CommentedOutCodeSniff.php)

```php
<?php

// ecs.php

declare(strict_types=1);

use Symplify\CodingStandard\Sniffs\Debug\CommentedOutCodeSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(CommentedOutCodeSniff::class);
};
```

```php
<?php

// $file = new File;
// $directory = new Diretory([$file]);
```

:x:
