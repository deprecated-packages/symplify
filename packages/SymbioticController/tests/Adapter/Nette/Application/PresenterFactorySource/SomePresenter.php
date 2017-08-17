<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Tests\Adapter\Nette\Application\PresenterFactorySource;

use Nette\Application\UI\Presenter;

final class SomePresenter extends Presenter
{
    public function renderDefault(): void
    {
        echo 'Hi';
        $this->template->setFile(__DIR__ . '/templates/default.latte');
    }

    protected function startup(): void
    {
        parent::startup();
        // for testing reasons
        $this->autoCanonicalize = false;
    }
}
