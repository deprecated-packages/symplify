<?php declare (strict_types=1);

namespace Symplify\EasyHydrator\Tests\Fixture;

final class PersonsCollection
{
    /**
     * @var Person[]
     */
    private $persons;

    /**
     * @var array<string, Person>
     */
    private $indexedPersons;

    /**
     * @param Person[] $persons
     * @param array<string, Person> $indexedPersons
     */
    public function __construct(array $persons, array $indexedPersons)
    {
        $this->persons = $persons;
        $this->indexedPersons = $indexedPersons;
    }

    /**
     * @return Person[]
     */
    public function getPersons(): array
    {
        return $this->persons;
    }

    /**
     * @return array<string, Person>
     */
    public function getIndexedPersons(): array
    {
        return $this->indexedPersons;
    }
}
