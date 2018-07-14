<?php declare(strict_types=1);

namespace Symplify\Statie\Twig\Tests\Renderable;

use Symplify\PackageBuilder\Finder\SymfonyFileInfoFactory;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Renderable\File\FileFactory;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;
use Symplify\Statie\Twig\Exception\InvalidTwigSyntaxException;
use Symplify\Statie\Twig\Renderable\TwigFileDecorator;
use Twig\Loader\ArrayLoader;

final class TwigFileDecoratorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var TwigFileDecorator
     */
    private $twigFileDecorator;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    protected function setUp(): void
    {
        $this->twigFileDecorator = $this->container->get(TwigFileDecorator::class);
        $this->fileFactory = $this->container->get(FileFactory::class);

        /** @var ArrayLoader $arrayLoader */
        $arrayLoader = $this->container->get(ArrayLoader::class);
        $arrayLoader->setTemplate('default', file_get_contents(__DIR__ . '/TwigFileDecoratorSource/default.twig'));
    }

    public function testDecorateFile(): void
    {
        $file = $this->createFileFromFilePath(__DIR__ . '/TwigFileDecoratorSource/fileWithoutLayout.twig');
        $this->twigFileDecorator->decorateFiles([$file]);

        $this->assertContains('Contact me!', $file->getContent());
    }

    public function testDecorateFileWithLayout(): void
    {
        $file = $this->createFileFromFilePath(__DIR__ . '/TwigFileDecoratorSource/contact.twig');
        $file->addConfiguration([
            'layout' => 'default',
        ]);

        $this->twigFileDecorator->decorateFiles([$file]);

        $this->assertStringEqualsFile(__DIR__ . '/TwigFileDecoratorSource/expectedContact.html', $file->getContent());
    }

    public function testDecorateFileWithFileVariable(): void
    {
        $file = $this->createFileFromFilePath(__DIR__ . '/TwigFileDecoratorSource/fileWithFileVariable.twig');
        $this->twigFileDecorator->decorateFiles([$file]);

        $this->assertContains('fileWithFileVariable.twig', $file->getContent());
    }

    public function testDecorateFileWithInvalidTwigSyntax(): void
    {
        $fileWithInvalidTwigSyntax = __DIR__ . '/TwigFileDecoratorSource/fileWithInvalidTwigSyntax.twig';
        $file = $this->createFileFromFilePath($fileWithInvalidTwigSyntax);

        $this->expectException(InvalidTwigSyntaxException::class);

        $file->addConfiguration([
            'layout' => 'default',
        ]);

        $this->twigFileDecorator->decorateFiles([$file]);
    }

    public function testDecorateFileWithHighlightedTwigCode(): void
    {
        $file = $this->createFileFromFilePath(__DIR__ . '/TwigFileDecoratorSource/fileWithHighlightedCode.twig');

        $file->addConfiguration([
            'layout' => 'default',
        ]);

        $this->twigFileDecorator->decorateFiles([$file]);

        $this->assertStringEqualsFile(__DIR__ . '/TwigFileDecoratorSource/expectedWithHighlightedCode.html', $file->getContent());
    }

    private function createFileFromFilePath(string $filePath): AbstractFile
    {
        $fileInfo = SymfonyFileInfoFactory::createFromFilePath($filePath);

        return $this->fileFactory->createFromFileInfo($fileInfo);
    }
}
