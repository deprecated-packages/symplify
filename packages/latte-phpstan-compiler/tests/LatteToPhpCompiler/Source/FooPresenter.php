<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler\Tests\LatteToPhpCompiler\Source;

use Nette\Application\UI\Presenter;

final class FooPresenter extends Presenter
{
    public function renderDefault(int $limit, ?array $add = null): void
    {
    }

    public function actionGrid(int $limit, ?array $add = null): void
    {
    }

    public function renderGrid(int $limit, ?array $add = null): void
    {
    }

    public function handleDoSomething(string $foo): void
    {
    }
}
