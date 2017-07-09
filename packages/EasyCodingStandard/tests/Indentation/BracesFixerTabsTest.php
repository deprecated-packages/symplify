<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Indentation;

use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;

final class BracesFixerTabsTest extends TestCase
{
    public function test(): void
    {
        $container = (new ContainerFactory)->createWithConfig(
            __DIR__ . '/IndentationSource/config-with-braces-fixer-and-tab-indentation.neon'
        );

        /** @var Configuration $configuration */
        $configuration = $container->get(Configuration::class);
        $configuration->resolveFromArray([
            'isFixer' => true,
        ]);

        /** @var FixerFileProcessor $fixerFileProcessor */
        $fixerFileProcessor = $container->get(FixerFileProcessor::class);

        $fixedFile = __DIR__ . '/IndentationFixedSource/FixedClassWithTabs.php';
        $fixedContent = file_get_contents($fixedFile);
        $testedFile = __DIR__ . '/IndentationSource/temp.php';
        file_put_contents(
            $testedFile,
            str_replace("\t", '    ', $fixedContent)
        );

        $fixerFileProcessor->processFile(new SplFileInfo($testedFile));

        $this->assertFileEquals(
            __DIR__ . '/IndentationFixedSource/FixedClassWithTabs.php',
            $testedFile
        );

        FileSystem::delete($testedFile);
    }
}
