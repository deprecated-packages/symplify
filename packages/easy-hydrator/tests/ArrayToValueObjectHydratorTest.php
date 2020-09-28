<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator\Tests;

use DateTimeImmutable;
use DateTimeInterface;
use Symplify\EasyHydrator\ArrayToValueObjectHydrator;
use Symplify\EasyHydrator\Tests\Fixture\ImmutableTimeEvent;
use Symplify\EasyHydrator\Tests\Fixture\Person;
use Symplify\EasyHydrator\Tests\Fixture\PersonWithAge;
use Symplify\EasyHydrator\Tests\Fixture\TimeEvent;
use Symplify\EasyHydrator\Tests\HttpKernel\EasyHydratorTestKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

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

        $this->arrayToValueObjectHydrator = self::$container->get(ArrayToValueObjectHydrator::class);
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

    public function testRetypeStringToInteger(): void
    {
        $personWithAge = $this->arrayToValueObjectHydrator->hydrateArray([
            'name' => 'John',
            // retype this
            'age' => '50',
        ], PersonWithAge::class);

        $this->assertInstanceOf(PersonWithAge::class, $personWithAge);

        /** @var PersonWithAge $personWithAge */
        $this->assertSame(50, $personWithAge->getAge());
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
}
