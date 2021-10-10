<?php

declare(strict_types=1);

namespace Symplify\PHPStanLatteRules\Tests\Rules\LatteCompleteCheckRule\Fixture;

use Nette\Application\UI\Control;
use Symplify\PHPStanLatteRules\Tests\Rules\LatteCompleteCheckRule\Source\ExampleModel;

final class GetTemplateAndReplaceExtension extends Control
{
    /** @var ExampleModel[] */
    private $listOfObjects = [];

    public function render(): void
    {
        $this->getTemplate()->existingVariable = '2021-09-11';
        $this->getTemplate()->listOfObjects = $this->listOfObjects;
        $this->getTemplate()->setFile(__DIR__ . '/../Source/' . str_replace('php', 'latte', pathinfo(__FILE__, PATHINFO_BASENAME)));
        $this->getTemplate()->render();
    }

    protected function createComponentExampleSubControl(): InvalidControlRenderArguments
    {
        return new InvalidControlRenderArguments();
    }
}
