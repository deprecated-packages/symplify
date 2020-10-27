<?php declare (strict_types=1);

namespace Symplify\EasyHydrator\Tests\Fixture;

final class DefaultValuesConstructor
{
    /**
     * @var string|null
     */
    private $foo;

    /**
     * @var string
     */
    private $bar;

    /**
     * @var Person|null
     */
    private $person;


    public function __construct(
        ?string $foo,
        string $bar = 'bar',
        ?Person $person = null
    ) {
        $this->foo = $foo;
        $this->bar = $bar;
        $this->person = $person;
    }

    public function getFoo(): ?string
    {
        return $this->foo;
    }

    public function getBar(): string
    {
        return $this->bar;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }
}
