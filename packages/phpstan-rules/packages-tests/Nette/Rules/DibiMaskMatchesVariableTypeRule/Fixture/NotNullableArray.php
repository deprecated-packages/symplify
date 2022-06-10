<?php

namespace Symplify\PHPStanRules\Tests\Nette\Rules\DibiMaskMatchesVariableTypeRule\Fixture;

namespace Symplify\PHPStanRules\Tests\Nette\Rules\DibiMaskMatchesVariableTypeRule\Fixture;

final class NotNullableArray
{
    public function run()
    {
        $params = [];
        if ($this->getIds() !== null) {
            $params['i%in'] = 'string';
        }

        return $params;
    }

    public function getIds(): array|null
    {
        return [];
    }
}
