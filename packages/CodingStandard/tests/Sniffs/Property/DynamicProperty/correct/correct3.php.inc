<?php

class SomeClass extends \Nette\Application\UI\Presenter
{
    public function __construct()
    {
        $this->autoCanonicalize = true;
    }
}
