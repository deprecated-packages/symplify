<?php declare (strict_types=1);

namespace Symplify\EasyHydrator\Tests\Fixture;

final class PersonsCollection
{
    /**
     * @var Person[]
     */
    private $persons;

    /**
     * @param Person[] $persons
     */
    public function __construct(array $persons)
    {
        $this->persons = $persons;
    }

    /**
     * @return Person[]
     */
    public function getPersons(): array
    {
        return $this->persons;
    }
}
