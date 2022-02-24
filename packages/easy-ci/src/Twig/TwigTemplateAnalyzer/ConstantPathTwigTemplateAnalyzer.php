<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Twig\TwigTemplateAnalyzer;

use Nette\Utils\Strings;
use Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
use Symplify\EasyCI\Twig\Contract\TwigTemplateAnalyzerInterface;
use Symplify\EasyCI\ValueObject\LineAwareFileError;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ConstantPathTwigTemplateAnalyzer implements TwigTemplateAnalyzerInterface
{
    /**
     * @see https://regex101.com/r/BK5zQ2/1
     * @var string
     */
    private const PATH_WITH_NAME_REGEX = '#path\((\'|\")(?<route_name>\w+)#';

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return FileErrorInterface[]
     */
    public function analyze(array $fileInfos): array
    {
        $templateErrors = [];
        foreach ($fileInfos as $fileInfo) {
            $matches = Strings::matchAll($fileInfo->getContents(), self::PATH_WITH_NAME_REGEX, PREG_OFFSET_CAPTURE);

            foreach ($matches as $match) {
                $errorMessage = sprintf(
                    'Route name "%s" in path() function should be replaced by constant to avoid typos and loose on renames.',
                    (string) $match['route_name'][0]
                );
                $line = $this->resolveLineNumber($fileInfo, $match);

                $templateErrors[] = new LineAwareFileError($errorMessage, $fileInfo, $line);
            }
        }

        return $templateErrors;
    }

    /**
     * @param array<int|string, mixed> $match
     */
    private function resolveLineNumber(SmartFileInfo $fileInfo, array $match): int
    {
        $length = $match[0][1];
        return substr_count(substr($fileInfo->getContents(), 0, $length), "\n") + 1;
    }
}
