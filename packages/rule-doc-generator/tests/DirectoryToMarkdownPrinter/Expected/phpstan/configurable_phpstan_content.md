# Rules Overview

## SomePHPStanRule

Some description

:wrench: **configure it!**

- class: `Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Fixture\ConfigurablePHPStan\SomePHPStanRule`

```yaml
services:
    -
        class: Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Fixture\ConfigurablePHPStan\SomePHPStanRule
        tags: [phpstan.rules.rule]
        arguments:
            someValue: 10
```

↓

```php
bad code
```

:x:

```php
good code
```

:+1:

<br>
