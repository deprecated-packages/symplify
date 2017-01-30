<?php


class SomePresenter
{
    /**
     * @var stdClass
     */
    private $stdClass;

    /**
     * @var DateTime
     */
    private $dateTime;

    public function inject(stdClass $stdClass)
    {
        $this->stdClass = $stdClass;
    }

    public function __construct(DateTime $dateTime)
    {
        $this->dateTime = $dateTime;
    }
}
