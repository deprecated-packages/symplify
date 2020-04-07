# Hydrate Arrays to Objects

[![Downloads total](https://img.shields.io/packagist/dt/symplify/easy-hydrator.svg?style=flat-square)](https://packagist.org/packages/symplify/easy-hydrator/stats)

- **easy!**
- PHP 7.4 support
- constructor injection support
- auto-resolving of `DateTimeInterface` string value
- auto-retype based on param type declarations
- cached

## Install

```bash
composer require symplify/easy-hydrator
```

## Usage

Having value object with constructor injection:

```php
<?php

declare(strict_types=1);

namespace App\ValueObject;

use DateTimeInterface;

final class Person
{
    private string $name;

    private int $age;

    private DateTimeInterface $metAt;

    public function __construct(string $name, int $age, DateTimeInterface $metAt)
    {
        $this->name = $name;
        $this->age = $age;
        $this->metAt = $metAt;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function getMetAt(): DateTimeInterface
    {
        return $this->metAt;
    }
}
```

Use hydrator with array like this:

```php
<?php

declare(strict_types=1);

namespace App\Repository;

use App\ValueObject\Person;
use Symplify\EasyHydrator\ArrayToValueObjectHydrator;

final class HumanRepository
{
    /**
     * @var ArrayToValueObjectHydrator
     */
    private $arrayToValueObjectHydrator;

    public function __construct(ArrayToValueObjectHydrator $arrayToValueObjectHydrator)
    {
        $this->arrayToValueObjectHydrator = $arrayToValueObjectHydrator;
    }

    public function getPerson(): Person
    {
        $person = $this->arrayToValueObjectHydrator->hydrateArray([
            'name' => 'Tom',
            // will be retyped to int
            'age' => '30',
            // will be retyped to DateTimeInterface
            'metAt' => '2020-02-02',
        ], Person::class);

        // ...

        return $person;
    }
}
```

### Multiple Value Objects?

This is how you hydrate 1 item:

```php
<?php

$singlePersonAsArray = [
    'name' => 'Tom',
    // will be retyped to int
    'age' => '30',
    // will be retyped to DateTimeInterface
    'metAt' => '2020-02-02',
]);

/** @var Person $person */
$person = $this->arrayToValueObjectHydrator->hydrateArray($singlePersonAsArray, Person::class);
```

But how can we hydrate multiple items?

```php
$manyPersonsAsArray = [];
$manyPersonsAsArray[] = [
    'name' => 'Tom',
    // will be retyped to int
    'age' => '30',
    // will be retyped to DateTimeInterface
    'metAt' => '2020-02-02',
];

$manyPersonsAsArray[] = [
    'name' => 'John',
    // will be retyped to int
    'age' => '25',
    // will be retyped to DateTimeInterface
    'metAt' => '2019-12-31',
];

/** @var Person[] $persons */
$persons = $this->arrayToValueObjectHydrator->hydrateArrays($manyPersonsAsArray, Person::class);
```
