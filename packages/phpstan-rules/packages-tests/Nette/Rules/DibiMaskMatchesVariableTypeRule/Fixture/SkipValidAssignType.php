<?php

namespace Symplify\PHPStanRules\Tests\Nette\Rules\DibiMaskMatchesVariableTypeRule\Fixture;

namespace Symplify\PHPStanRules\Tests\Nette\Rules\DibiMaskMatchesVariableTypeRule\Fixture;

final class SkipValidAssignType
{
    public function run()
    {
        $params = [];
        $params['i%in'] = ['string'];

        return $params;
    }
}
