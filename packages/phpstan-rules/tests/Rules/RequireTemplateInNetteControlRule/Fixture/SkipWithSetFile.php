<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireTemplateInNetteControlRule\Fixture;

use Nette\Application\UI\Control;

final class SkipWithSetFile extends Control
{
    public function render()
    {
        $this->template->setFile('someFile.latte');
    }
}
