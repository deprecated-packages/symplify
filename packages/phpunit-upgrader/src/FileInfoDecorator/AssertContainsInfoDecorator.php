<?php

declare(strict_types=1);

namespace Symplify\PHPUnitUpgrader\FileInfoDecorator;

use Nette\Utils\Strings;
use Symplify\PHPUnitUpgrader\AssertContainsFileLineExtractor;
use Symplify\PHPUnitUpgrader\ValueObject\FilePathWithContent;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\PHPUnitUpgrader\Tests\FileInfoDecorator\AssertContainsInfoDecorator\AssertContainsInfoDecoratorTest
 */
final class AssertContainsInfoDecorator
{
    /**
     * @see https://regex101.com/r/RpK2uw/1
     * @var string
     */
    private const ASSERT_CONTAINS_REGEX = '#assertContains#';

    /**
     * @var AssertContainsFileLineExtractor
     */
    private $assertContainsFileLineExtractor;

    public function __construct(AssertContainsFileLineExtractor $assertContainsFileLineExtractor)
    {
        $this->assertContainsFileLineExtractor = $assertContainsFileLineExtractor;
    }

    public function decorate(FilePathWithContent $filePathWithContent, SmartFileInfo $errorReportFileInfo): string
    {
        $fileLines = $this->assertContainsFileLineExtractor->extract($errorReportFileInfo);

        $currentFileLineContents = $filePathWithContent->getContentLines();

        foreach ($fileLines as $fileLine) {
            if (! Strings::endsWith($fileLine->getFilePath(), $filePathWithContent->getFilePath())) {
                continue;
            }

            foreach ($currentFileLineContents as $currentLineNumber => $currentLineContent) {
                if ($fileLine->getLine() !== $currentLineNumber) {
                    continue;
                }

                $newLineContent = Strings::replace(
                    $currentLineContent,
                    self::ASSERT_CONTAINS_REGEX,
                    'assertStringContainsString'
                );

                if ($newLineContent === $currentLineContent) {
                    continue;
                }

                $filePathWithContent->changeLineContent($currentLineNumber, $newLineContent);
            }
        }

        return $filePathWithContent->getCurrentFileContent();
    }
}
