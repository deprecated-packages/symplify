<?php

namespace Symplify\PHPStanRules\Tests\Nette\Rules\DibiMaskMatchesVariableTypeRule\Fixture;

namespace Symplify\PHPStanRules\Tests\Nette\Rules\DibiMaskMatchesVariableTypeRule\Fixture;

final class SkipValidArray
{
    public function run()
    {
        $params = [
            'i%in' => ['string'],
        ];

        return $params;
    }
}
