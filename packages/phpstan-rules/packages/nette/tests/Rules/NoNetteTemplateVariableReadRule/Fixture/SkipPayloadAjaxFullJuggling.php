<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\NoNetteTemplateVariableReadRule\Fixture;

use Nette\Application\UI\Presenter;

abstract class SkipPayloadAjaxFullJuggling extends Presenter
{
    public function render()
    {
        $this->payload = $this->template;
    }
}
