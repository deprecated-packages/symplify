<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Tests\Adapter\Nette\Application\PresenterFactorySource;

use Nette\Application\UI\Presenter;

class SomePresenter extends Presenter
{
    protected function startup(): void
    {
        parent::startup();
        // for testing reasons
        $this->autoCanonicalize = false;
    }

    public function renderDefault()
    {
        echo 'Hi';
        $this->template->setFile(__DIR__ . '/templates/default.latte');
    }
}
