# 1 Rules Overview

## SomeConfiguredFixer

Some description

:wrench: **configure it!**

- class: [`Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Fixture\PHPCSFixer\Configurable\SomeConfiguredFixer`](Fixture/PHPCSFixer/Configurable/SomeConfiguredFixer.php)

```php
<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Fixture\PHPCSFixer\Configurable\SomeConfiguredFixer;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->ruleWithConfiguration(SomeConfiguredFixer::class, [
        SomeConfiguredFixer::LOCAL_CONSTANT => 'value',
    ]);
};
```

↓

```diff
-bad code
+good code
```

<br>
