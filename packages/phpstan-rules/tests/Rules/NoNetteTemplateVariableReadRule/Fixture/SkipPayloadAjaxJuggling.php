<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNetteTemplateVariableReadRule\Fixture;

use Nette\Application\UI\Presenter;

abstract class SkipPayloadAjaxJuggling extends Presenter
{
    public function render()
    {
        // magic
        $this->payload->key = $this->template->key;

        $this->payload = $this->template;
    }
}
