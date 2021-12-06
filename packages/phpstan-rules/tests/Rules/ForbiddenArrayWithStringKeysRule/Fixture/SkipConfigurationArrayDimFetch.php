<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenArrayWithStringKeysRule\Fixture;

use Nette\Neon\Neon;

final class SkipConfigurationArrayDimFetch
{
    /**
     * @param string[] $classNames
     */
    public function createNeonFileContent(array $classNames): array
    {
        $neon['services'] = [
            'some_keys' => $classNames
        ];

        $neon['nested']['services'] = [
            'some_keys' => $classNames
        ];

        return $neon;
    }
}
