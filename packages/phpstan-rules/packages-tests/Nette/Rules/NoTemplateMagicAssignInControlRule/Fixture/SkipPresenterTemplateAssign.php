<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Nette\Rules\NoTemplateMagicAssignInControlRule\Fixture;

use Nette\Application\UI\Presenter;

final class SkipPresenterTemplateAssign extends Presenter
{
    public function render()
    {
        $this->template->key = 'value';
    }
}
