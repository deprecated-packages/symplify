<?php declare (strict_types=1);

namespace Symplify\EasyHydrator\Tests\Fixture;

final class Marriage
{
    /**
     * @var Person
     */
    private $personA;

    /**
     * @var Person
     */
    private $personB;

    private $date;


    public function __construct(\DateTimeInterface $date, Person $personA, Person $personB)
    {
        $this->personA = $personA;
        $this->personB = $personB;
        $this->date = $date;
    }

    public function getPersonA(): Person
    {
        return $this->personA;
    }

    public function getPersonB(): Person
    {
        return $this->personB;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }
}
