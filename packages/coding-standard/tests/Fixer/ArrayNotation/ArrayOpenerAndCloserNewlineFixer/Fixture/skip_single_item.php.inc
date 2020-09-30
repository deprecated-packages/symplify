<?php

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer\Fixture;

final class SkipSingleItem
{
    public function run()
    {
        $value = [
            RenameMethodRector::OLD_TO_NEW_METHODS_BY_CLASS => inline_objects(
                [new MethodCallRename('Cake\Form\Form', 'errors', 'getErrors')]
            )
        ];
    }
}
