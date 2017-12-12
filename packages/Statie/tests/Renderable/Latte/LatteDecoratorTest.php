<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable\Latte;

use Symfony\Component\Finder\SplFileInfo;
use Symplify\Statie\Exception\Latte\InvalidLatteSyntaxException;
use Symplify\Statie\FlatWhite\Latte\DynamicStringLoader;
use Symplify\Statie\Renderable\File\File;
use Symplify\Statie\Renderable\File\FileFactory;
use Symplify\Statie\Renderable\LatteFileDecorator;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;

final class LatteDecoratorTest extends AbstractContainerAwareTestCase
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

        /** @var DynamicStringLoader $dynamicStringLoader */
        $dynamicStringLoader = $this->container->get(DynamicStringLoader::class);
        $dynamicStringLoader->changeContent(
            'default',
            file_get_contents(__DIR__ . '/LatteDecoratorSource/default.latte')
        );
    }

    public function testDecorateFile(): void
    {
        $file = $this->createFileFromFilePath(__DIR__ . '/LatteDecoratorSource/fileWithoutLayout.latte');
        $this->latteFileDecorator->decorateFiles([$file]);

        $this->assertContains('Contact me!', $file->getContent());
    }

    public function testDecorateFileWithLayout(): void
    {
        $file = $this->createFileFromFilePath(__DIR__ . '/LatteDecoratorSource/contact.latte');
        $file->addConfiguration([
            'layout' => 'default',
        ]);

        $this->latteFileDecorator->decorateFiles([$file]);

        $this->assertStringEqualsFile(
            __DIR__ . '/LatteDecoratorSource/expectedContact.html',
            $file->getContent()
        );
    }

    public function testDecorateFileWithFileVariable(): void
    {
        $file = $this->createFileFromFilePath(__DIR__ . '/LatteDecoratorSource/fileWithFileVariable.latte');
        $this->latteFileDecorator->decorateFiles([$file]);

        $this->assertContains('fileWithFileVariable.latte', $file->getContent());
    }

    public function testDecorateFileWithInvalidLatteSyntax(): void
    {
        $fileWithInvalidLatteSyntax = __DIR__ . '/LatteDecoratorSource/fileWithInvalidLatteSyntax.latte';
        $file = $this->createFileFromFilePath($fileWithInvalidLatteSyntax);

        $this->expectException(InvalidLatteSyntaxException::class);
        $this->expectExceptionMessage(sprintf(
            'Invalid Latte syntax found or missing value in "%s" file: Unknown macro {iff}, did you mean {if}?',
            $fileWithInvalidLatteSyntax
        ));

        $this->latteFileDecorator->decorateFiles([$file]);
    }

    private function createFileFromFilePath(string $filePath): File
    {
        $fileInfo = new SplFileInfo($filePath, '', '');
        return $this->fileFactory->createFromFileInfo($fileInfo);
    }
}
