<?php

declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Tests\Adapter\Nette\NetteEvent\DispatchSource;

use Nette\Application\UI\Presenter;

final class HomepagePresenter extends Presenter
{
    public function actionDefault()
    {
        $this->terminate();
    }
}
