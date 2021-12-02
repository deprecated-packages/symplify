<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenArrayWithStringKeysRule\Fixture;

use Nette\Neon\Neon;

final class SkipConfigurationFormat
{
    /**
     * @param string[] $classNames
     */
    public function createNeonFileContent(array $classNames): string
    {
        $neon = [
            'services' => [
                'some_keys' => $classNames
            ],
        ];

        return Neon::encode($neon, Neon::BLOCK, '    ');
    }
}
