# 3 Rules Overview

## ConfigurableRector

Some change

:wrench: **configure it!**

- class: [`Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Fixture\Rector\Configurable\ConfigurableRector`](Fixture/Rector/Configurable/ConfigurableRector.php)

```php
<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Fixture\Rector\Configurable\ConfigurableRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(ConfigurableRector::class, [['key' => 'value', 'second_key' => 'second_value']]);
};
```

↓

```diff
-before
+after
```

<br>

## DirectConfiguredStringKeyRector

Some change

:wrench: **configure it!**

- class: [`Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Fixture\Rector\Configurable\DirectConfiguredStringKeyRector`](Fixture/Rector/Configurable/DirectConfiguredStringKeyRector.php)

```php
<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Fixture\Rector\Configurable\DirectConfiguredStringKeyRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(DirectConfiguredStringKeyRector::class, ['view' => 'Laravel\Templating\render']);
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
use Rector\Config\RectorConfig;
use Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Fixture\Rector\Configurable\WithPHPStanTypeObject;
use Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Source\SomeValueObjectWrapper;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(WithPHPStanTypeObject::class, [new SomeValueObjectWrapper(new ObjectType('SomeObject'))]);
};
```

↓

```diff
-before
+after
```

<br>
