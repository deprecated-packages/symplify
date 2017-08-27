<?php // this should be moved to correct when fixed

class SomeController
{
    public function renderAction()
    {
        new \Symplify\CodingStandard\Tests\Sniffs\DependencyInjection\NoClassInstantiation\Source\EntityClass;
    }
}
