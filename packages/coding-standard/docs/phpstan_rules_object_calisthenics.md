# PHPStan Rules - Object Calisthenics

Install:

```bash
composer require symplify/coding-standard --dev
```

Include full rules:

```yaml
# phsptan.neon
includes:
    - vendor/symplify/coding-standard/packages/object-calisthenics/config/object-calisthenics-rules.neon
```

## Rule 1: Only X Level of Indentation per Method

- class: [`SingleIndentationInMethodRule`](../packages/object-calisthenics/src/Rules/SingleIndentationInMethodRule.php)
- **configuration allowed**

```yaml
# phpstan.neon
parameters:
    object_calisthenics:
        # default
        max_nesting_level: 1
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

<br>

## Rule 6: No Names Shorter than 3 Chars

- class: [`NoShortNameRule`](../packages/object-calisthenics/src/Rules/NoShortNameRule.php)
- **configuration allowed**

```yaml
# phsptan.neon
parameters:
    object_calisthenics:
        # defaults
        min_name_lenght: 3
        allowed_short_names: ['id']
```

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

<br>

## Rule 7: Keep Your Classes Small

- class: [`TooLongClassLikeRule`](../packages/object-calisthenics/src/Rules/TooLongClassLikeRule.php)
- **configuration allowed**

```yaml
# phpstan.neon
parameters:
    object_calisthenics:
        # default
        max_class_like_length: 300
```

```php
<?php declare(strict_types=1);

final class SomeClass
{
    // 300 lines
}
```

:x:

---

- class: [`TooLongFunctionLikeRule`](../packages/object-calisthenics/src/Rules/TooLongFunctionLikeRule.php)
- **configuration allowed**

```yaml
# phpstan.neon
parameters:
    object_calisthenics:
        # default
        max_function_like_length: 20
```

```php
<?php declare(strict_types=1);

final class SomeClass
{
    public function run()
    {
        // 20 lines
    }
}
```

:x:

---

- class: [`TooManyPropertiesRule`](../packages/object-calisthenics/src/Rules/TooManyPropertiesRule.php)
- **configuration allowed**

```yaml
# phpstan.neon
parameters:
    symplify:
        # default
        max_property_count: 15
```

```php
<?php declare(strict_types=1);

final class SomeClass
{
    public $value;

    public $value2;

    private $value3;

    private $value4;

    private $value5;

    private $value6;

    // ...
}
```

:x:

---

- class: [`TooManyMethodsRule`](../packages/object-calisthenics/src/Rules/TooManyMethodsRule.php)
- **configuration allowed**

```yaml
# phpstan.neon
parameters:
    symplify:
        # default
        max_method_count: 15
```

```php
<?php declare(strict_types=1);

final class SomeClass
{
    public function go()
    {
    }

    public function run()
    {
    }

    public function too()
    {
    }
}
```

:x:

<br>

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
