<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\Tests\LattePersistence\Source;

use Nette\Application\UI\Presenter;

final class SomePresenter extends Presenter
{
    public function link(string $destination, $args = []): string
    {
        return $destination . '/' . implode('/', $args[0] ?? []);
    }
}
