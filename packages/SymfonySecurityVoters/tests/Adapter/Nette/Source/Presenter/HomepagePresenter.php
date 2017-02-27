<?php declare(strict_types=1);

namespace Symplify\SymfonySecurityVoters\Tests\Adapter\Nette\Source\Presenter;

use Nette\Application\UI\Presenter;

final class HomepagePresenter extends Presenter
{
    public function actionDefault(): void
    {
        $this->terminate();
    }
}
