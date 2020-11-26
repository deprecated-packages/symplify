# Hydrate Arrays to Objects

[![Downloads total](https://img.shields.io/packagist/dt/symplify/easy-hydrator.svg?style=flat-square)](https://packagist.org/packages/symplify/easy-hydrator/stats)

- **easy!**
- PHP 7.4 support
- constructor injection support
- auto-resolving of `DateTimeInterface` string value
- auto-retype based on param type declarations
- nested objects support
- customizable objects creation
- cached

## Install

```bash
composer require symplify/easy-hydrator
```

Add to `config/bundles.php`:

```php
return [
    Symplify\EasyHydrator\EasyHydratorBundle::class => [
        'all' => true,
    ],
    Symplify\SimplePhpDocParser\Bundle\SimplePhpDocParserBundle::class => [
        'all' => true,
    ],
];
```

## Usage

Having value object with constructor injection:

```php
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
        return $this->arrayToValueObjectHydrator->hydrateArray([
            'name' => 'Tom',
            // will be retyped to int
            'age' => '30',
            // will be retyped to DateTimeInterface
            'metAt' => '2020-02-02',
        ], Person::class);

        // ...
    }
}
```

### Multiple Value Objects?

This is how you hydrate 1 item:

```php
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

### Optionable values

If object has optional parameters, and some of their values are not provided in data, default value is used in the hydrated object.

```php
class MyObject
{
    private string $foo;

    private string $bar;

    public function __construct(string $foo, string $bar = 'bar')
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }

    public function getFoo(): string
    {
        return $this->foo;
    }

    public function getBar(): string
    {
        return $this->bar;
    }
}

$data = [
    'foo' => 'foo',
];

$object = $this->arrayToValueObjectHydrator->hydrateArray($data, MyObject::class);
// bar
$object->getBar();
```

### Missing constructor data

When not provided data for required constructor parameter, `Symplify\EasyHydrator\Exception\MissingDataException` is thrown.

## Contribute

The sources of this package are contained in the symplify monorepo. We welcome contributions for this package at [symplify/symplify](https://github.com/symplify/symplify).
