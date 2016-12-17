<?php

declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Tests\NetteEvent\DispatchSource;

use Nette\Application\Responses\TextResponse;
use Nette\Application\UI\Presenter;

final class ResponsePresenter extends Presenter
{
    protected function startup()
    {
        parent::startup();
        $this->autoCanonicalize = false;
    }

    public function actionDefault()
    {
        $this->sendResponse(new TextResponse(null));
    }
}
