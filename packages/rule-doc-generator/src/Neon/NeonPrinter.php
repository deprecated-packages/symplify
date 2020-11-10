<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Neon;

use Nette\Neon\Encoder;
use Nette\Neon\Neon;
use Nette\Utils\Strings;

final class NeonPrinter
{
    public const TAGS_REGEX = '#tags:\s+\-\s+(?<tag>.*?)$#ms';

    /**
     * @param mixed[] $neon
     */
    public function print(array $neon): string
    {
        $neonContent = Neon::encode($neon, Encoder::BLOCK);

        // inline single tags, dummy
        $neonContent = Strings::replace($neonContent, self::TAGS_REGEX, 'tags: [$1]');

        // fix double space in arguments
        $neonContent = Strings::replace($neonContent, '#\n(\n\s+arguments:)#ms', '$1');

        return Strings::replace($neonContent, '#\t#', '    ');
    }
}
