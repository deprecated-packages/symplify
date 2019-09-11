<?php declare(strict_types=1);

namespace Symplify\Statie\Twig\Tests\Renderable;

use Nette\Utils\FileSystem;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\Statie\Configuration\StatieConfiguration;
use Symplify\Statie\HttpKernel\StatieKernel;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Renderable\File\FileFactory;
use Symplify\Statie\Twig\Exception\InvalidTwigSyntaxException;
use Symplify\Statie\Twig\Renderable\TwigFileDecorator;
use Twig\Loader\ArrayLoader;

final class TwigFileDecoratorTest extends AbstractKernelTestCase
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
        $this->bootKernel(StatieKernel::class);

        $this->twigFileDecorator = self::$container->get(TwigFileDecorator::class);
        $this->fileFactory = self::$container->get(FileFactory::class);

        $configuration = self::$container->get(StatieConfiguration::class);
        $configuration->setSourceDirectory(__DIR__ . '/TwigFileDecoratorSource');

        $arrayLoader = self::$container->get(ArrayLoader::class);
        $arrayLoader->setTemplate('default', FileSystem::read(__DIR__ . '/TwigFileDecoratorSource/default.twig'));
    }

    public function testDecorateFile(): void
    {
        $file = $this->createFileFromFilePath(__DIR__ . '/TwigFileDecoratorSource/fileWithoutLayout.twig');
        $this->twigFileDecorator->decorateFiles([$file]);
        $this->assertStringContainsString('Contact me!', $file->getContent());
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

        $this->assertStringContainsString('fileWithFileVariable.twig', $file->getContent());
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

        $this->assertStringEqualsFile(
            __DIR__ . '/TwigFileDecoratorSource/expectedWithHighlightedCode.html',
            $file->getContent()
        );
    }

    private function createFileFromFilePath(string $filePath): AbstractFile
    {
        $fileInfo = new SmartFileInfo($filePath);

        return $this->fileFactory->createFromFileInfo($fileInfo);
    }
}
