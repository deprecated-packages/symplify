<?php declare(strict_types=1);

namespace Symplify\Statie\Latte\Tests\Renderable;

use Nette\Utils\FileSystem;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\Statie\Configuration\StatieConfiguration;
use Symplify\Statie\Latte\Exception\InvalidLatteSyntaxException;
use Symplify\Statie\Latte\Loader\ArrayLoader;
use Symplify\Statie\Latte\Renderable\LatteFileDecorator;
use Symplify\Statie\Renderable\File\File;
use Symplify\Statie\Renderable\File\FileFactory;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;

final class LatteFileDecoratorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var LatteFileDecorator
     */
    private $latteFileDecorator;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    protected function setUp(): void
    {
        $this->latteFileDecorator = $this->container->get(LatteFileDecorator::class);
        $this->fileFactory = $this->container->get(FileFactory::class);

        $configuration = $this->container->get(StatieConfiguration::class);
        $configuration->setSourceDirectory(__DIR__ . '/LatteFileDecoratorSource');

        $arrayLoader = $this->container->get(ArrayLoader::class);
        $arrayLoader->changeContent('default', FileSystem::read(__DIR__ . '/LatteFileDecoratorSource/default.latte'));
    }

    public function testDecorateFile(): void
    {
        $file = $this->createFileFromFilePath(__DIR__ . '/LatteFileDecoratorSource/fileWithoutLayout.latte');
        $this->latteFileDecorator->decorateFiles([$file]);

        $this->assertContains('Contact me!', $file->getContent());
    }

    public function testDecorateFileWithLayout(): void
    {
        $file = $this->createFileFromFilePath(__DIR__ . '/LatteFileDecoratorSource/contact.latte');
        $file->addConfiguration([
            'layout' => 'default',
        ]);

        $this->latteFileDecorator->decorateFiles([$file]);

        $this->assertStringEqualsFile(__DIR__ . '/LatteFileDecoratorSource/expectedContact.html', $file->getContent());
    }

    public function testDecorateFileWithFileVariable(): void
    {
        $file = $this->createFileFromFilePath(__DIR__ . '/LatteFileDecoratorSource/fileWithFileVariable.latte');
        $this->latteFileDecorator->decorateFiles([$file]);

        $this->assertContains('fileWithFileVariable.latte', $file->getContent());
    }

    public function testDecorateFileWithInvalidLatteSyntax(): void
    {
        $fileWithInvalidLatteSyntax = __DIR__ . '/LatteFileDecoratorSource/fileWithInvalidLatteSyntax.latte';
        $file = $this->createFileFromFilePath($fileWithInvalidLatteSyntax);

        $this->expectException(InvalidLatteSyntaxException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Invalid Latte syntax found or missing value in "%s" file: Unknown macro {iff}, did you mean {if}?',
                $fileWithInvalidLatteSyntax
            )
        );

        $this->latteFileDecorator->decorateFiles([$file]);
    }

    public function testHighlightedCode(): void
    {
        $file = $this->createFileFromFilePath(__DIR__ . '/LatteFileDecoratorSource/fileWithHighlightedCode.latte');

        $file->addConfiguration([
            'layout' => 'default',
        ]);

        $this->latteFileDecorator->decorateFiles([$file]);

        $this->assertStringEqualsFile(
            __DIR__ . '/LatteFileDecoratorSource/expectedFileWithHighlightedCode.html',
            $file->getContent()
        );
    }

    private function createFileFromFilePath(string $filePath): File
    {
        $fileInfo = new SmartFileInfo($filePath);

        return $this->fileFactory->createFromFileInfo($fileInfo);
    }
}
