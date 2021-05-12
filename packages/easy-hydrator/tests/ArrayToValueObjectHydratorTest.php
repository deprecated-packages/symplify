<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator\Tests;

use DateTimeImmutable;
use DateTimeInterface;
use Symplify\EasyHydrator\ArrayToValueObjectHydrator;
use Symplify\EasyHydrator\Tests\Fixture\Arrays;
use Symplify\EasyHydrator\Tests\Fixture\ImmutableTimeEvent;
use Symplify\EasyHydrator\Tests\Fixture\Marriage;
use Symplify\EasyHydrator\Tests\Fixture\Person;
use Symplify\EasyHydrator\Tests\Fixture\PersonsCollection;
use Symplify\EasyHydrator\Tests\Fixture\PersonWithAge;
use Symplify\EasyHydrator\Tests\Fixture\TimeEvent;
use Symplify\EasyHydrator\Tests\HttpKernel\EasyHydratorTestKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class ArrayToValueObjectHydratorTest extends AbstractKernelTestCase
{
    /**
     * @var array<int, array<string, string>>
     */
    private const TIME_EVENTS_DATA = [
        [
            'when' => '2020-02-02',
        ],
        [
            'when' => '2020-04-04',
        ],
    ];

    /**
     * @var ArrayToValueObjectHydrator
     */
    private $arrayToValueObjectHydrator;

    protected function setUp(): void
    {
        $this->bootKernel(EasyHydratorTestKernel::class);

        $this->arrayToValueObjectHydrator = $this->getService(ArrayToValueObjectHydrator::class);
    }

    public function test(): void
    {
        $person = $this->arrayToValueObjectHydrator->hydrateArray([
            'name' => 'Tom',
        ], Person::class);

        $this->assertInstanceOf(Person::class, $person);

        /** @var Person $person */
        $this->assertSame('Tom', $person->getName());
    }

    public function testWithoutRetype(): void
    {
        $marriage = $this->arrayToValueObjectHydrator->hydrateArray([
            'personA' => new Person('A'),
            'personB' => new Person('B'),
            'date' => new DateTimeImmutable('2020-02-03'),
        ], Marriage::class);

        $this->assertInstanceOf(Marriage::class, $marriage);

        /** @var Marriage $marriage */
        $person = $marriage->getPersonA();
        $this->assertSame('A', $person->getName());
    }

    public function testRetypes(): void
    {
        $personWithAge = $this->arrayToValueObjectHydrator->hydrateArray([
            'name' => 'John',
            // retype this
            'age' => '50',
        ], PersonWithAge::class);

        $this->assertInstanceOf(PersonWithAge::class, $personWithAge);

        /** @var PersonWithAge $personWithAge */
        $this->assertSame(50, $personWithAge->getAge());

        // retype scalar arrays
        $data = [
            'integers' => ['1', 2.0],
            'floats' => ['1.1', 2],
            'booleans' => ['true', '0'],
            'strings' => [1, 2.2],
        ];

        /** @var Arrays $actual */
        $actual = $this->arrayToValueObjectHydrator->hydrateArray($data, Arrays::class);

        $this->assertSame([1, 2], $actual->getIntegers());
        $this->assertSame([1.1, 2.0], $actual->getFloats());
        $this->assertSame([true, false], $actual->getBooleans());
        $this->assertSame(['1', '2.2'], $actual->getStrings());
    }

    public function testDateTimeImmutable(): void
    {
        $timeEvent = $this->arrayToValueObjectHydrator->hydrateArray([
            'when' => '2020-02-02',
        ], ImmutableTimeEvent::class);

        $this->assertInstanceOf(ImmutableTimeEvent::class, $timeEvent);

        /** @var ImmutableTimeEvent $timeEvent */
        $this->assertInstanceOf(DateTimeImmutable::class, $timeEvent->getWhen());
    }

    public function testDateTime(): void
    {
        $timeEvent = $this->arrayToValueObjectHydrator->hydrateArray([
            'when' => '2020-02-02',
        ], TimeEvent::class);

        $this->assertInstanceOf(TimeEvent::class, $timeEvent);

        /** @var TimeEvent $timeEvent */
        $this->assertInstanceOf(DateTimeInterface::class, $timeEvent->getWhen());
    }

    public function testMultipleImmutable(): void
    {
        $timeEvents = $this->arrayToValueObjectHydrator->hydrateArrays(
            self::TIME_EVENTS_DATA,
            ImmutableTimeEvent::class
        );

        $this->assertCount(2, $timeEvents);

        foreach ($timeEvents as $timeEvent) {
            $this->assertInstanceOf(ImmutableTimeEvent::class, $timeEvent);
        }
    }

    public function testMultiple(): void
    {
        $timeEvents = $this->arrayToValueObjectHydrator->hydrateArrays(self::TIME_EVENTS_DATA, TimeEvent::class);
        $this->assertCount(2, $timeEvents);
        foreach ($timeEvents as $timeEvent) {
            $this->assertInstanceOf(TimeEvent::class, $timeEvent);
        }
    }

    public function testMultipleArrays(): void
    {
        $data = [
            [
                'integers' => [1, 2],
                'floats' => [1.1, 2.2],
                'booleans' => [true, false],
                'strings' => ['a', 'b'],
            ],
            [
                'integers' => [3, 4],
                'floats' => [3.3, 4.24],
                'booleans' => [false, true],
                'strings' => ['c', 'd'],
            ],
        ];

        /** @var Arrays[] $arrayOfArrays */
        $arrayOfArrays = $this->arrayToValueObjectHydrator->hydrateArrays($data, Arrays::class);

        $this->assertCount(2, $arrayOfArrays);
        $this->assertContainsOnlyInstancesOf(Arrays::class, $arrayOfArrays);

        $this->assertArraysHasValidTypes($arrayOfArrays);
    }

    public function testMultipleRecursiveObjects(): void
    {
        $data = [
            [
                'date' => '2019-06-21',
                'personA' => [
                    'name' => 'John Doe 1',
                ],
                'personB' => [
                    'name' => 'Jane Doe 1',
                ],
            ], [
                'date' => '2019-06-22',
                'personA' => [
                    'name' => 'John Doe 2',
                ],
                'personB' => [
                    'name' => 'Jane Doe 2',
                ],
            ],
        ];

        $marriages = $this->arrayToValueObjectHydrator->hydrateArrays($data, Marriage::class);

        $this->assertCount(2, $marriages);
        $this->assertContainsOnlyInstancesOf(Marriage::class, $marriages);
    }

    public function testMultipleRecursiveArrayOfObjects(): void
    {
        $data = [
            [
                'persons' => [
                    [
                        'name' => 'John Doe 1',
                    ],
                    [
                        'name' => 'Jane Doe 1',
                    ],
                ],
                'indexedPersons' => [
                    'HOMER' => [
                        'name' => 'Homer',
                    ],
                ],
            ],
            [
                'persons' => [
                    [
                        'name' => 'John Doe 2',
                    ],
                    [
                        'name' => 'Jane Doe 2',
                    ],
                ],
                'indexedPersons' => [
                    'HOMER' => [
                        'name' => 'Homer',
                    ],
                ],
            ],
        ];

        /** @var PersonsCollection[] $personsCollections */
        $personsCollections = $this->arrayToValueObjectHydrator->hydrateArrays($data, PersonsCollection::class);

        $this->assertCount(2, $personsCollections);

        foreach ($personsCollections as $personsCollection) {
            $persons = $personsCollection->getPersons();

            $this->assertCount(2, $persons);
            $this->assertContainsOnlyInstancesOf(Person::class, $persons);

            $indexedPersons = $personsCollection->getIndexedPersons();
            $this->assertCount(1, $indexedPersons);
            $this->assertArrayHasKey('HOMER', $indexedPersons);
            $this->assertContainsOnlyInstancesOf(Person::class, $indexedPersons);
        }
    }

    /**
     * @param Arrays[] $arrayOfArrays
     */
    private function assertArraysHasValidTypes(array $arrayOfArrays): void
    {
        foreach ($arrayOfArrays as $arrays) {
            $integers = $arrays->getIntegers();
            foreach ($integers as $integer) {
                $this->assertIsInt($integer);
            }

            $floats = $arrays->getFloats();
            foreach ($floats as $float) {
                $this->assertIsFloat($float);
            }

            $strings = $arrays->getStrings();
            foreach ($strings as $string) {
                $this->assertIsString($string);
            }

            $booleans = $arrays->getBooleans();
            foreach ($booleans as $bool) {
                $this->assertIsBool($bool);
            }
        }
    }
}
