<?php

namespace Symplify\PHPStanRules\Tests\Nette\Rules\DibiMaskMatchesVariableTypeRule\Fixture;

namespace Symplify\PHPStanRules\Tests\Nette\Rules\DibiMaskMatchesVariableTypeRule\Fixture;

final class InvalidAssignType
{
    public function run()
    {
        $params = [];
        $params['i%in'] = 'string';

        return $params;
    }
}
