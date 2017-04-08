<?php


class SomePresenter
{
    /**
     * @var stdClass
     */
    private $stdClass;

    public function __construct(stdClass $stdClass)
    {
    	$this->stdClass = $stdClass;
    }
}
