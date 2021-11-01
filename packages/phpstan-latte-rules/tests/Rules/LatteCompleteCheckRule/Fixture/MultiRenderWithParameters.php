<?php

declare(strict_types=1);

namespace Symplify\PHPStanLatteRules\Tests\Rules\LatteCompleteCheckRule\Fixture;

use Nette\Application\UI\Control;
use Symplify\PHPStanLatteRules\Tests\Rules\LatteCompleteCheckRule\Source\ExampleModel;

final class MultiRenderWithParameters extends Control
{
    /** @var ExampleModel[] */
    private $listOfObjects = [];

    public function render(): void
    {
        if ($this->listOfObjects) {
            $this->template->render(__DIR__ . '/../Source/ExampleControl.latte', [
                'listOfObjects' => $this->listOfObjects,
            ]);
            return;
        }

        $this->template->render(__DIR__ . '/../Source/ExampleControl.latte', [
            'existingVariable' => '2021-09-11',
        ]);
    }

    protected function createComponentExampleSubControl(): InvalidControlRenderArguments
    {
        return new InvalidControlRenderArguments();
    }
}
