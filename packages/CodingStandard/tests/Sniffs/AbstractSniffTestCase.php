<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;

abstract class AbstractSniffTestCase extends TestCase
{
    /**
     * @var SniffFileProcessor
     */
    private $sniffFileProcessor;

    protected function setUp(): void
    {
        $container = (new ContainerFactory())->create();

        $this->sniffFileProcessor = $container->get(SniffFileProcessor::class);
    }

    protected function doTest(string $input, string $expected): void
    {
        $this->sniffFileProcessor->setSingleSniff($this->createSniff());

        $fileInfo = new SplFileInfo($input, '', '');
        $result = $this->sniffFileProcessor->processFile($fileInfo);

        $this->assertStringEqualsFile($expected, $result);
    }

    abstract protected function createSniff(): Sniff;
}
