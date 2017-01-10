<?php declare(strict_types=1);
namespace SomeNamespace;

class correct2
{

    public function run()
    {
        /** @var Presenter $presenter */
        $presenter = $di->getService('IPresenter');
    }
}
