<?php declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Tests\Adapter\Nette\NetteEvent\DispatchSource;

use Nette\Application\UI\Presenter;

final class ResponsePresenter extends Presenter
{
    protected function startup(): void
    {
        parent::startup();
        $this->autoCanonicalize = false;
    }

    public function renderDefault()
    {
        dump('EEE');
        die;
        return 5;
    }


}
