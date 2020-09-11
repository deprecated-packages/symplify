# PHPStan Rules - No Arrays

## Use value Object over String to Object Arrays

- class: [`NoArrayStringObjectReturnRule`](../src/Rules/NoArrayStringObjectReturnRule.php)

```php
<?php

final class SomeClass
{
    /**
     * @return array<string, stdClass>
     */
    private function getValues()
    {
        // ...
    }
}
```

:x:

```php
<?php

final class SomeClass
{
    /**
     * @return WrappingValueObject[]
     */
    private function getValues()
    {
        // ...
    }
}
```

:+1:

<br>

## Array with String Keys is not allowed, Use Value Object instead

- class: [`ForbiddenArrayWithStringKeysRule`](../src/Rules/ForbiddenArrayWithStringKeysRule.php)

```php
<?php declare(strict_types=1);

final class SomeClass
{
    public function run()
    {
        return [
            'name' => 'John',
            'surname' => 'Dope',
        ];
    }
}
```

:x:

```php
<?php declare(strict_types=1);

final class SomeClass
{
    public function run()
    {
        return new Person('John', 'Dope');
    }
}
```

:+1:

<br>

## Array Destruct is not Allowed, use Value Object instead

- class: [`ForbiddenArrayDestructRule`](../src/Rules/ForbiddenArrayDestructRule.php)

```php
<?php declare(strict_types=1);

final class SomeClass
{
    public function run(): void
    {
        [$firstValue, $secondValue] = $this->getRandomData();
    }
}
```

:x:

```php
<?php declare(strict_types=1);

final class SomeClass
{
    public function run(): void
    {
        $resultValueObject = $this->getRandomData();
        $firstValue = $resultValueObject->getFirstValue();
        $secondValue = $resultValueObject->getSecondValue();
    }
}
```

:+1:

<br>

## Use Value Object over Return of Values

- class: [`NoReturnArrayVariableList`](../src/Rules/NoReturnArrayVariableList.php)

```php
<?php declare(strict_types=1);

final class ReturnVariables
{
    /**
     * @return mixed[a
     */
    public function run($value, $value2): array
    {
        return [$value, $value2];
    }
}
```

:x:

```php
<?php declare(strict_types=1);

final class ReturnVariables
{
    public function run($value, $value2): ValueObject
    {
        return new ValueObject($value, $value2);
    }
}
```

:+1:

<br>

## Use Value Object over Mutli Array Dim Fetch Assigns

- class: [`NoMultiArrayAssignRule`](../src/Rules/NoMultiArrayAssignRule.php)

```php
final class SomeClass
{
    public function run()
    {
        $values = [];
        $values['some'] = [];
        $values['some'] = [];
    }
}
```

:x:
