<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Tests\Adapter\Nette\Application\PresenterFactorySource;

use Nette\Application\Responses\TextResponse;

final class StandalonePresenter
{
    public function __invoke()
    {
        return new TextResponse('Hey');
    }
}
