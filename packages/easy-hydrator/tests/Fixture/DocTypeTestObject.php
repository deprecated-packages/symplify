<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator\Tests\Fixture;

class DocTypeTestObject
{
    /**
     * @var string[]
     */
    private $props1;

    /**
     * @var string[]|null
     */
    private $props2;

    /**
     * @var array<string>
     */
    private $props3;

    /**
     * @var array<string>|null
     */
    private $props4;

    /**
     * @var array<int, string>
     */
    private $props5;

    /**
     * @var array<int, string>|null
     */
    private $props6;

    /**
     * @var Person[]
     */
    private $props11;

    /**
     * @var Person[]|null
     */
    private $props12;

    /**
     * @var array<Person>
     */
    private $props13;

    /**
     * @var array<Person>|null
     */
    private $props14;

    /**
     * @var array<int, Person>
     */
    private $props15;

    /**
     * @var null|array<int, Person>
     */
    private $props16;

    /**
     * @param string[] $props1
     * @param string[]|null $props2
     * @param array<string> $props3
     * @param array<string>|null $props4
     * @param array<int, string> $props5
     * @param array<int, string>|null $props6
     * @param Person[] $props11
     * @param Person[]|null $props12
     * @param array<Person> $props13
     * @param array<Person>|null $props14
     * @param array<int, Person> $props15
     * @param null|array<int, Person> $props16
     */
    public function __construct(
        array $props1,
        ?array $props2,
        array $props3,
        ?array $props4,
        array $props5,
        ?array $props6,
        array $props11,
        ?array $props12,
        array $props13,
        ?array $props14,
        array $props15,
        ?array $props16
    ) {
        $this->props1 = $props1;
        $this->props2 = $props2;
        $this->props3 = $props3;
        $this->props4 = $props4;
        $this->props5 = $props5;
        $this->props6 = $props6;
        $this->props11 = $props11;
        $this->props12 = $props12;
        $this->props13 = $props13;
        $this->props14 = $props14;
        $this->props15 = $props15;
        $this->props16 = $props16;
    }

    /**
     * @return string[]
     */
    public function getProps1(): array
    {
        return $this->props1;
    }

    /**
     * @return string[]|null
     */
    public function getProps2(): ?array
    {
        return $this->props2;
    }

    /**
     * @return array<string>
     */
    public function getProps3(): array
    {
        return $this->props3;
    }

    /**
     * @return array<string>|null
     */
    public function getProps4(): ?array
    {
        return $this->props4;
    }

    /**
     * @return array<int, string>
     */
    public function getProps5(): array
    {
        return $this->props5;
    }

    /**
     * @return array<int, string>|null
     */
    public function getProps6(): ?array
    {
        return $this->props6;
    }

    /**
     * @return Person[]
     */
    public function getProps11(): array
    {
        return $this->props11;
    }

    /**
     * @return Person[]|null
     */
    public function getProps12(): ?array
    {
        return $this->props12;
    }

    /**
     * @return array<Person>
     */
    public function getProps13(): array
    {
        return $this->props13;
    }

    /**
     * @return array<Person>|null
     */
    public function getProps14(): ?array
    {
        return $this->props14;
    }

    /**
     * @return array<int, Person>
     */
    public function getProps15(): array
    {
        return $this->props15;
    }

    /**
     * @return null|array<int, Person>
     */
    public function getProps16(): ?array
    {
        return $this->props16;
    }
}
