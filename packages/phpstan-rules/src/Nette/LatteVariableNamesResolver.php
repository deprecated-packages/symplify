<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette;

use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileSystem;

final class LatteVariableNamesResolver
{
    /**
     * @see https://regex101.com/r/MXuoHp/1
     * @var string
     */
    private const VARIABLE_NAME_REGEX = '#\$(?<' . self::NAME_PART . '>\w+)#';

    /**
     * E.g. foreached single variable
     * @see https://regex101.com/r/SrXwyh/1
     * @var string
     */
    private const TEMPLATE_MADE_NAME_REGEX = '#as(\s+)\$(?<' . self::NAME_PART . '>\w+)#';

    /**
     * @var string
     */
    private const NAME_PART = 'name';

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    public function __construct(SmartFileSystem $smartFileSystem)
    {
        $this->smartFileSystem = $smartFileSystem;
    }

    /**
     * @return string[]
     */
    public function resolveFromFile(string $filePath): array
    {
        $latteFileContent = $this->smartFileSystem->readFile($filePath);

        $variableNames = $this->resolveNameMatchesByPattern($latteFileContent, self::VARIABLE_NAME_REGEX);
        $templateMadeVariableNames = $this->resolveNameMatchesByPattern(
            $latteFileContent,
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
