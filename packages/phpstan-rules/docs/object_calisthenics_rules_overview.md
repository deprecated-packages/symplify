# 3 Rules Overview

## NoChainMethodCallRule

Do not use chained method calls. Put each on separated lines.

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\ObjectCalisthenics\Rules\NoChainMethodCallRule`](../packages/object-calisthenics/src/Rules/NoChainMethodCallRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\ObjectCalisthenics\Rules\NoChainMethodCallRule
        tags: [phpstan.rules.rule]
        arguments:
            allowedChainTypes:
                - AllowedFluent
```

↓

```php
$this->runThis()->runThat();

$fluentClass = new AllowedFluent();
$fluentClass->one()->two();
```

:x:

<br>

```php
$this->runThis();
$this->runThat();

$fluentClass = new AllowedFluent();
$fluentClass->one()->two();
```

:+1:

<br>

## NoElseAndElseIfRule

Do not use "else/elseif". Refactor to early return

- class: [`Symplify\PHPStanRules\ObjectCalisthenics\Rules\NoElseAndElseIfRule`](../packages/object-calisthenics/src/Rules/NoElseAndElseIfRule.php)

```php
if (...) {
    return 1;
} else {
    return 2;
}
```

:x:

<br>

```php
if (...) {
    return 1;
}

return 2;
```

:+1:

<br>

## NoShortNameRule

Do not name "%s", shorter than %d chars

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\ObjectCalisthenics\Rules\NoShortNameRule`](../packages/object-calisthenics/src/Rules/NoShortNameRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\ObjectCalisthenics\Rules\NoShortNameRule
        tags: [phpstan.rules.rule]
        arguments:
            minNameLength: 3
```

↓

```php
function is()
{
}
```

:x:

<br>

```php
function isClass()
{
}
```

:+1:

<br>
