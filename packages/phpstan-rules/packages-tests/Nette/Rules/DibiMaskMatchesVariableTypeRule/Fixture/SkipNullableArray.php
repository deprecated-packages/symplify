<?php

namespace Symplify\PHPStanRules\Tests\Nette\Rules\DibiMaskMatchesVariableTypeRule\Fixture;

namespace Symplify\PHPStanRules\Tests\Nette\Rules\DibiMaskMatchesVariableTypeRule\Fixture;

final class SkipNullableArray
{
    public function run()
    {
        $params = [];
        if ($this->getIds() !== null) {
            $params['i%in'] = $this->getIds();
        }

        return $params;
    }

    public function getIds(): array|null
    {
        return [];
    }
}
