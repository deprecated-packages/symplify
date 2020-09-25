<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Console\Output;

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\Console\Output\JsonOutputFormatter;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class JsonOutputFormatterTest extends AbstractKernelTestCase
{
    /**
     * @var JsonOutputFormatter
     */
    private $jsonOutputFormatter;

    /**
     * @var ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCodingStandardKernel::class);

        $this->jsonOutputFormatter = self::$container->get(JsonOutputFormatter::class);
        $this->errorAndDiffCollector = self::$container->get(ErrorAndDiffCollector::class);
    }

    public function test(): void
    {
        $jsonContent = $this->jsonOutputFormatter->createJsonContent($this->errorAndDiffCollector);
        $this->assertStringMatchesFormatFile(__DIR__ . '/Fixture/expected_plain.json', $jsonContent . PHP_EOL);

        $randomFileInfo = new SmartFileInfo(__DIR__ . '/Source/RandomFile.php');
        $this->errorAndDiffCollector->addErrorMessage($randomFileInfo, 100, 'Error message', ArraySyntaxFixer::class);

        $this->errorAndDiffCollector->addDiffForFileInfo($randomFileInfo, 'some diff', [LineLengthFixer::class]);
        $this->errorAndDiffCollector->addDiffForFileInfo($randomFileInfo, 'some other diff', [LineLengthFixer::class]);

        $jsonContent = $this->jsonOutputFormatter->createJsonContent($this->errorAndDiffCollector);
        $this->assertStringMatchesFormatFile(__DIR__ . '/Fixture/expected_json_output.json', $jsonContent . PHP_EOL);
    }
}
