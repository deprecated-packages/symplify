<?php

namespace Symplify\PHPStanRules\Nette\Tests\Rules\DibiMaskMatchesVariableTypeRule\Fixture;

namespace Symplify\PHPStanRules\Nette\Tests\Rules\DibiMaskMatchesVariableTypeRule\Fixture;

final class SkipValidAssignType
{
    public function run()
    {
        $params = [];
        $params['i%in'] = ['string'];

        return $params;
    }
}
