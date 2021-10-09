<?php

declare(strict_types=1);

namespace Symplify\PHPStanLatteRules\Tests\Rules\LatteCompleteCheckRule\Fixture;

use Nette\Application\UI\Control;
use Nette\Bridges\ApplicationLatte\Template;

/**
 * @property Template $template
 */
final class InvalidControlRenderArguments extends Control
{
    public function render(string $name)
    {
        $this->template->render(__DIR__ . '/../Source/render_control.latte', [
            'someType' => 'some...',
        ]);
    }

    public function createComponentSomeName(): self
    {
        return new self();
    }
}
