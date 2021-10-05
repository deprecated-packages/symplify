<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\LatteCompleteCheckRuleTemplateSetup\Fixtures\PropertyReadTemplate;

use Nette\Application\UI\Control;
use Nette\Bridges\ApplicationLatte\Template;
use Symplify\PHPStanRules\Nette\Tests\Rules\LatteCompleteCheckRuleTemplateSetup\Source\ExampleModel;

/**
 * @property-read Template $template
 */
final class ExampleControl extends Control
{
    /** @var ExampleModel[] */
    private $listOfObjects = [];

    public function render(?int $param = null): void
    {
        $this->template->existingVariable = '2021-09-11';
        $this->template->listOfObjects = $this->listOfObjects;
        $this->template->setFile(__DIR__ . '/../../templates/ExampleControl.latte');
        $this->template->render();
    }

    protected function createComponentExampleSubControl(): ExampleControl
    {
        return new ExampleControl();
    }
}
