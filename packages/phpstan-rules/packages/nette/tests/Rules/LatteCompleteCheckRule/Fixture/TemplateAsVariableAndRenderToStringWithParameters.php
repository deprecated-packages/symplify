<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\LatteCompleteCheckRule\Fixture;

use Nette\Application\UI\Control;
use Nette\Bridges\ApplicationLatte\Template;
use Symplify\PHPStanRules\Nette\Tests\Rules\LatteCompleteCheckRule\Source\ExampleModel;

final class TemplateAsVariableAndRenderToStringWithParameters extends Control
{
    /** @var ExampleModel[] */
    private $listOfObjects = [];

    public function render(): void
    {
        /** @var Template $template */
        $template = $this->getTemplate();
        $template->renderToString(__DIR__ . '/../Source/ExampleControl.latte', [
            'existingVariable' => '2021-09-11',
            'listOfObjects' => $this->listOfObjects,
        ]);
    }

    protected function createComponentExampleSubControl(): InvalidControlRenderArguments
    {
        return new InvalidControlRenderArguments();
    }
}
