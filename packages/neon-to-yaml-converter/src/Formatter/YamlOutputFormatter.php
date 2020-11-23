<?php

declare(strict_types=1);

namespace Symplify\NeonToYamlConverter\Formatter;

use Nette\Utils\Strings;

final class YamlOutputFormatter
{
    /**
     * @see https://regex101.com/r/aw2HtQ/1
     * @var string
     */
    private const INLINE_CALSS_IMPORT_REGEX = '#(calls|imports):\s\|#';

    /**
     * @see https://regex101.com/r/yGWPqK/1
     * @var string
     */
    private const FACTORY_REGEX = '#factory:\s+\- (.*?)\n\s+\- (.*?)\n#ms';

    /**
     * @see https://regex101.com/r/3nuSOy/1
     * @var string
     */
    private const MULTI_LINES_REGEX = '#^(\w)#ms';

    public function format(string $content): string
    {
        // new lines between main parts
        $content = Strings::replace($content, self::MULTI_LINES_REGEX, PHP_EOL . '$1');

        // factory inline
        $content = Strings::replace($content, self::FACTORY_REGEX, 'factory: [$1, $2]' . PHP_EOL);

        // calls inline fixup
        $content = Strings::replace($content, self::INLINE_CALSS_IMPORT_REGEX, '$1:');

        return trim($content) . PHP_EOL;
    }
}
