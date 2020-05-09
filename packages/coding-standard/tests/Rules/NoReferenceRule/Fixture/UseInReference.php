<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoReferenceRule\Fixture;

use Nette\Utils\Strings;

final class UseInReference
{
    public function someMethod($filePath)
    {
        return Strings::replace(
            $filePath,
            '#{(.*?)}#m',
            function (array $match) use (&$i, $arguments) {
            }
        );
    }
}
