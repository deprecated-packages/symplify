<?php


class SomePresenter
{
    /**
     * @var stdClass
     */
    private $stdClass;

    public function inject(stdClass $stdClass)
    {
        $this->stdClass = $stdClass;
    }
}
