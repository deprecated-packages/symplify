# PHPStan Rules - Object Calisthenics

## Rule 1: Only X Level of Indentation per Method

- class: [`SingleIndentationInMethodRule`](../packages/object-calisthenics/src/Rules/SingleIndentationInMethodRule.php)
- **configuration allowed**

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\ObjectCalisthenics\Rules\SingleIndentationInMethodRule

parameters:
    object_calisthenics:
        # [default: 1]
        max_nesting_level: 2
```

```php
<?php declare(strict_types=1);

final class ManyIndentations
{
    public function someMethod()
    {
        if (true) {
            if (false) {
                return 'maybe';
            }
        }

        return 'sure';
    }
}
```

:x:

```php
<?php declare(strict_types=1);

final class SingleIndentation
{
    public function someMethod()
    {
        if (true) {
            return 'maybe';
        }

        return 'sure';
    }
}
```

:+1:

<br>

## Rule 2: No `else` And `elseif`

- class: [`NoElseAndElseIfRule`](../packages/object-calisthenics/src/Rules/NoElseAndElseIfRule.php)

```php
<?php declare(strict_types=1);

if ($value) {
    return 5;
} else {
    return 10;
}
```

:x:

```php
<?php declare(strict_types=1);

if ($value) {
    return 5;
}

return 10;
```

:+1:

<br>

## Rule 5: No Chain Method Call

- class: [`NoChainMethodCallRule`](../packages/object-calisthenics/src/Rules/NoChainMethodCallRule.php)
- Check [Fluent Interfaces are Evil](https://ocramius.github.io/blog/fluent-interfaces-are-evil/) and [Fluent Interfaces Are Bad for Maintainability
](https://www.yegor256.com/2018/03/13/fluent-interfaces.html)

```php
<?php declare(strict_types=1);

class SomeClass
{
    public function run()
    {
        return $this->create()->modify()->save();
    }
}
```

:x:

```php
<?php declare(strict_types=1);

class SomeClass
{
    public function run()
    {
        $object = $this->create();
        $object->modify();
        $object->save();

        return $object;
    }
}
```

:+1:


## Rule 6: No Names Shorter than 3 Chars

- class: [`NoShortNameRule`](../packages/object-calisthenics/src/Rules/NoShortNameRule.php)

```php
<?php declare(strict_types=1);

final class EM
{
}
```

:x:

```php
<?php declare(strict_types=1);

final class EverestMule
{
}
```

:+1:


## Rule 7 - Keep Your Classes Small

@todo



## Rule 9: No Setter Methods

- class: [`NoSetterClassMethodRule`](../packages/object-calisthenics/src/Rules/NoSetterClassMethodRule.php)

```php
<?php declare(strict_types=1);

final class Person
{
    private string $name;

    // should be "__construct"
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
```

:x:
