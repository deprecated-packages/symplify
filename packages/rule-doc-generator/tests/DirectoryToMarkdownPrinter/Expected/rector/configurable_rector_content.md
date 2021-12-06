# 2 Rules Overview

## ConfigurableRector

Some change

:wrench: **configure it!**

- class: [`Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Fixture\Rector\Configurable\ConfigurableRector`](Fixture/Rector/Configurable/ConfigurableRector.php)

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Fixture\Rector\Configurable\ConfigurableRector;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(ConfigurableRector::class)
        ->configure(['key' => 'value', 'second_key' => 'second_value']);
};
```

↓

```diff
-before
+after
```

<br>

## WithPHPStanTypeObject

Some change

:wrench: **configure it!**

- class: [`Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Fixture\Rector\Configurable\WithPHPStanTypeObject`](Fixture/Rector/Configurable/WithPHPStanTypeObject.php)

```php
<?php

declare(strict_types=1);

use PHPStan\Type\ObjectType;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Fixture\Rector\Configurable\WithPHPStanTypeObject;
use Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Source\SomeValueObjectWrapper;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(WithPHPStanTypeObject::class)
        ->configure([new SomeValueObjectWrapper(new ObjectType('SomeObject'))]);
};
```

↓

```diff
-before
+after
```

<br>
