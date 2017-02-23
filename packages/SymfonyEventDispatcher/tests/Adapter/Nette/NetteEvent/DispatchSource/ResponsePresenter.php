<?php


declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Tests\Adapter\Nette\NetteEvent\DispatchSource;

use Nette\Application\Responses\TextResponse;
use Nette\Application\UI\Presenter;

final class ResponsePresenter extends Presenter
{
    public function actionDefault()
    {
        $this->sendResponse(new TextResponse(null));
    }

    protected function startup()
    {
        parent::startup();
        $this->autoCanonicalize = false;
    }
}
