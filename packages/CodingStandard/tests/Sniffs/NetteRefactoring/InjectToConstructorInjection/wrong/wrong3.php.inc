<?php


class SomePresenter
{
    /**
     * @inject
     * @var stdClass
     */
    public $stdClass;

    /**
     * @var DateTime
     */
    private $dateTime;

    public function __construct(DateTime $dateTime)
    {
        $this->dateTime = $dateTime;
    }
}
