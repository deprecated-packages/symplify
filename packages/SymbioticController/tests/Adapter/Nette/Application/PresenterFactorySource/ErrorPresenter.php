<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Tests\Adapter\Nette\Application\PresenterFactorySource;

use Nette\Application\UI\Presenter;

final class ErrorPresenter extends Presenter
{
    protected function startup(): void
    {
        parent::startup();
        // for testing reasons
        $this->autoCanonicalize = false;
    }
}
