<?php

declare(strict_types=1);

namespace Symplify\PHPStanLatteRules\Tests\Rules\NoNetteRenderUnusedVariableRule\Fixture;

use Nette\Application\UI\Control;

final class SkipUnionTemplate extends Control
{
    public function render($random)
    {
        $firstLocationUsed = __DIR__ . '/../Source/union_template/first_location_used.latte';
        $secondLocationUnused = __DIR__ . '/../Source/union_template/second_location_unused.latte';

        $templatePath = $random ? $firstLocationUsed : $secondLocationUnused;

        $this->template->render($templatePath, [
            'unionVariable' => 'some_value'
        ]);
    }
}
