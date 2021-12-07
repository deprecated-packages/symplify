<?php

declare(strict_types=1);

namespace Symplify\PHPStanLatteRules\Tests\Rules\LatteCompleteCheckRule\Fixture;

use Nette\Application\UI\Presenter;
use Symplify\PHPStanLatteRules\Tests\Rules\LatteCompleteCheckRule\Source\ExampleModel;

final class OneActionPresenter extends Presenter
{
    /** @var ExampleModel[] */
    private $listOfObjects = [];

    public function renderDefault(): void
    {
        $this->template->existingVariable = '2021-09-11';
        $this->template->listOfObjects = $this->listOfObjects;
    }

    protected function createComponentExampleSubControl(): InvalidControlRenderArguments
    {
        return new InvalidControlRenderArguments();
    }
}
