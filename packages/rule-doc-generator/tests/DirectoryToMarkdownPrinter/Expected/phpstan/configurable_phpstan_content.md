# 1 Rules Overview

## SomePHPStanRule

Some description

:wrench: **configure it!**

- class: [`Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Fixture\PHPStan\Configurable\SomePHPStanRule`](Fixture/PHPStan/Configurable/SomePHPStanRule.php)

```yaml
services:
    -
        class: Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Fixture\PHPStan\Configurable\SomePHPStanRule
        tags: [phpstan.rules.rule]
        arguments:
            someValue: 10
```

↓

```php
bad code
```

:x:

<br>

```php
good code
```

:+1:

<br>
