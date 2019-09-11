<?php

use Symplify\CodingStandard\Tests\Sniffs\DependencyInjection\NoClassInstantiation\Source\EntityClass;

class SomeController
{
    public function renderAction()
    {
        $entityClass = new EntityClass;
    }
}
