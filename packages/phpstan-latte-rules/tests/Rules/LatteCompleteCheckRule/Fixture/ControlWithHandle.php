<?php

declare(strict_types=1);

namespace Symplify\PHPStanLatteRules\Tests\Rules\LatteCompleteCheckRule\Fixture;

use Nette\Application\UI\Control;

final class ControlWithHandle extends Control
{
    private string $bar;

    public function render(): void
    {
        $this->template->foo = 'foo';
        $this->template->bar = $this->bar;
        $this->template->setFile(__DIR__ . '/../Source/link.latte');
        $this->template->render();
    }

    public function handleDoSomething(string $foo, ?array $bar = null): void
    {
    }
}
