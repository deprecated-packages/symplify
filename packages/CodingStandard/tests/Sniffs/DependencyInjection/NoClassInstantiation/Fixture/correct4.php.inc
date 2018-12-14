<?php

class SomeController
{
    public function renderAction()
    {
        $entityClass = new \Symplify\CodingStandard\Tests\Sniffs\DependencyInjection\NoClassInstantiation\Source\EntityClass;
    }
}
