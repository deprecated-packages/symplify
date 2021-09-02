<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\NoTemplateMagicAssignInControlRule\Fixture;

use Nette\Application\UI\Presenter;

final class SkipPresenterTemplateAssign extends Presenter
{
    public function render()
    {
        $this->template->key = 'value';
    }
}
