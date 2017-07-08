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

    public function __construct(stdClass $stdClass, DateTime $dateTime)
    {
        $this->stdClass = $stdClass;
        $this->dateTime = $dateTime;
    }
}
