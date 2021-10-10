<?php

declare(strict_types=1);

namespace Symplify\PHPStanLatteRules\Tests\Rules\LatteCompleteCheckRule\Fixture;

use Nette\Application\UI\Control;
use Nette\Bridges\ApplicationLatte\Template;
use Symplify\PHPStanLatteRules\Tests\Rules\LatteCompleteCheckRule\Source\ExampleModel;

/**
 * @property-read Template $template
 */
final class PropertyReadTemplate extends Control
{
    /** @var ExampleModel[] */
    private $listOfObjects = [];

    public function render(): void
    {
        $this->template->existingVariable = '2021-09-11';
        $this->template->listOfObjects = $this->listOfObjects;
        $this->template->setFile(__DIR__ . '/../Source/ExampleControl.latte');
        $this->template->render();
    }

    protected function createComponentExampleSubControl(): InvalidControlRenderArguments
    {
        return new InvalidControlRenderArguments();
    }
}
