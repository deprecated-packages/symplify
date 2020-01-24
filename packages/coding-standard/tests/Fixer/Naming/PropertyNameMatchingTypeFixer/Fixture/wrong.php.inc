<?php

class SomeClass
{
    /**
     * @var EntityManager
     */
    private $eventManager;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var AMP
     */
    private $amp;

    /**
     * @var DateTime
     */
    private $letMeBe;

    /**
     * @var DateTimeImmutable
     */
    private $letMeBeToo;

    /**
     * @var \DateTimeInterface
     */
    private $letMeBeAsWell;

    /**
     * @var \Nette\Utils\DateTime
     */
    private $letMeBeAsForever;

    public function __construct(EntityManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }
}

?>
-----
<?php

class SomeClass
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var AMP
     */
    private $amp;

    /**
     * @var DateTime
     */
    private $letMeBe;

    /**
     * @var DateTimeImmutable
     */
    private $letMeBeToo;

    /**
     * @var \DateTimeInterface
     */
    private $letMeBeAsWell;

    /**
     * @var \Nette\Utils\DateTime
     */
    private $letMeBeAsForever;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
}

?>
