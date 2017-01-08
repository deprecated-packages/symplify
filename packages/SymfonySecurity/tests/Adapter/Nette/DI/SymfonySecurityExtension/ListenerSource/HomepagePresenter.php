<?php declare(strict_types=1); 

namespace Symplify\SymfonySecurity\Tests\Adapter\Nette\DI\SymfonySecurityExtension\ListenerSource;

use Nette\Application\UI\Presenter;

final class HomepagePresenter extends Presenter
{
    public function actionDefault()
    {
        $this->terminate();
    }
}
