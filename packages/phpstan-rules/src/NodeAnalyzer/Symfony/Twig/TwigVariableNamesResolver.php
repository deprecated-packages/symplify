<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer\Symfony\Twig;

use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileSystem;

final class TwigVariableNamesResolver
{
    /**
     * @see https://regex101.com/r/jf2v0i/1
     * @var string
     */
    private const VARIABLE_NAME_REGEX = '#{{\s+(?<' . self::NAME_PART . '>[\w_]+)\s+}}#';

    /**
     * E.g. foreached single variable - https://twig.symfony.com/doc/2.x/tags/for.html
     *
     * @see https://regex101.com/r/zx9iXU/1
     * @var string
     */
    private const TEMPLATE_MADE_NAME_REGEX = '#for\s+(?<' . self::NAME_PART . '>\w+)\s+in\s+\w+#';

    /**
     * @var string
     */
    private const NAME_PART = 'name';

    public function __construct(
        private SmartFileSystem $smartFileSystem
    ) {
    }

    /**
     * @return string[]
     */
    public function resolveFromFile(string $filePath): array
    {
        $fileContent = $this->smartFileSystem->readFile($filePath);
        $variableNames = $this->resolveNameMatchesByPattern($fileContent, self::VARIABLE_NAME_REGEX);

        $templateMadeVariableNames = $this->resolveNameMatchesByPattern(
            $fileContent,
            self::TEMPLATE_MADE_NAME_REGEX
        );

        return array_diff($variableNames, $templateMadeVariableNames);
    }

    /**
     * @return string[]
     */
    private function resolveNameMatchesByPattern(string $fileContent, string $regex): array
    {
        $names = [];

        $matches = Strings::matchAll($fileContent, $regex);
        foreach ($matches as $match) {
            $names[] = $match[self::NAME_PART];
        }

        return $names;
    }
}
