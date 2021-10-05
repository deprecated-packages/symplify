<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\LatteCompleteCheckRuleTemplateSetup\Fixtures\TemplateAsVariableAndRenderToStringWithParameters;

use Nette\Application\UI\Control;
use Nette\Bridges\ApplicationLatte\Template;
use Symplify\PHPStanRules\Nette\Tests\Rules\LatteCompleteCheckRuleTemplateSetup\Source\ExampleModel;

final class ExampleControl extends Control
{
    /** @var ExampleModel[] */
    private $listOfObjects = [];

    public function render(?int $param = null): void
    {
        /** @var Template $template */
        $template = $this->getTemplate();
        $template->renderToString(__DIR__ . '/../../templates/ExampleControl.latte', [
            'existingVariable' => '2021-09-11',
            'listOfObjects' => $this->listOfObjects,
        ]);
    }

    protected function createComponentExampleSubControl(): ExampleControl
    {
        return new ExampleControl();
    }
}
