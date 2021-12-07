<?php

declare(strict_types=1);

namespace Symplify\PHPStanLatteRules\Tests\Rules\LatteCompleteCheckRule\Fixture;

use Nette\Application\UI\Presenter;
use Symplify\PHPStanLatteRules\Tests\Rules\LatteCompleteCheckRule\Source\ExampleModel;

final class MultiActionsAndRendersPresenter extends Presenter
{
    /** @var ExampleModel[] */
    private $listOfObjects = [];

    public function actionOnlyAction(): void
    {
        $this->template->existingVariable = '2021-09-11';
        $this->template->listOfObjects = $this->listOfObjects;
    }

    public function renderOnlyRender(): void
    {
        $this->template->existingVariable = '2021-09-11';
        $this->template->listOfObjects = $this->listOfObjects;
    }

    public function actionActionAndRender(): void
    {
        // first variable is assigned in action
        $this->template->existingVariable = '2021-09-11';
    }

    public function renderActionAndRender(): void
    {
        // second variable is assigned in render
        $this->template->listOfObjects = $this->listOfObjects;
    }

    protected function createComponentExampleSubControl(): InvalidControlRenderArguments
    {
        return new InvalidControlRenderArguments();
    }
}
