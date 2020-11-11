<?php

declare(strict_types=1);

namespace Symplify\PackageScoper\Tests\ComposerJson\ScopedComposerJsonFactory;

use Symplify\ComposerJsonManipulator\ComposerJsonFactory;
use Symplify\ComposerJsonManipulator\Printer\ComposerJsonPrinter;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\PackageScoper\ComposerJson\ScopedComposerJsonFactory;
use Symplify\PackageScoper\HttpKernel\PackageScoperKernel;

final class ScopedComposerJsonFactoryTest extends AbstractKernelTestCase
{
    /**
     * @var ScopedComposerJsonFactory
     */
    private $scopedComposerJsonFactory;

    /**
     * @var ComposerJsonFactory
     */
    private $composerJsonFactory;

    /**
     * @var ComposerJsonPrinter
     */
    private $composerJsonPrinter;

    protected function setUp(): void
    {
        $this->bootKernel(PackageScoperKernel::class);
        $this->scopedComposerJsonFactory = self::$container->get(ScopedComposerJsonFactory::class);

        $this->composerJsonFactory = self::$container->get(ComposerJsonFactory::class);
        $this->composerJsonPrinter = self::$container->get(ComposerJsonPrinter::class);
    }

    public function test(): void
    {
        $composerJson = $this->composerJsonFactory->createFromFilePath(__DIR__ . '/Fixture/some_package_composer.json');
        $scopedComposerJson = $this->scopedComposerJsonFactory->createFromPackageComposerJson($composerJson);

        $scopedComposerJsonFileContent = $this->composerJsonPrinter->printToString($scopedComposerJson);

        $this->assertStringEqualsFile(
            __DIR__ . '/Fixture/Expected/expected_scoped_composer.json',
            $scopedComposerJsonFileContent
        );
    }
}
