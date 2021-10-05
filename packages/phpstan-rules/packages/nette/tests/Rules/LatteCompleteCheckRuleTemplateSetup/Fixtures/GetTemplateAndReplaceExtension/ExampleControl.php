<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\LatteCompleteCheckRuleTemplateSetup\Fixtures\GetTemplateAndReplaceExtension;

use Nette\Application\UI\Control;
use Symplify\PHPStanRules\Nette\Tests\Rules\LatteCompleteCheckRuleTemplateSetup\Source\ExampleModel;

final class ExampleControl extends Control
{
    /** @var ExampleModel[] */
    private $listOfObjects = [];

    public function render(?int $param = null): void
    {
        $this->getTemplate()->existingVariable = '2021-09-11';
        $this->getTemplate()->listOfObjects = $this->listOfObjects;
        $this->getTemplate()->setFile(__DIR__ . '/../../templates/' . str_replace('php', 'latte', pathinfo(__FILE__, PATHINFO_BASENAME)));
        $this->getTemplate()->render();
    }

    protected function createComponentExampleSubControl(): ExampleControl
    {
        return new ExampleControl();
    }
}
