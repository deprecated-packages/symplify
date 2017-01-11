<?php
namespace SomeNamespace;

class correct2
{

    public function run()
    {
        /** @var Presenter $presenter */
        $presenter = $di->getService('IPresenter');
    }
}
