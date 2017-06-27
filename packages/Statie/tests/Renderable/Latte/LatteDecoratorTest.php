<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable\Latte;

use SplFileInfo;
use Symplify\Statie\FlatWhite\Latte\DynamicStringLoader;
use Symplify\Statie\Renderable\File\FileFactory;
use Symplify\Statie\Renderable\Latte\LatteDecorator;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;

final class LatteDecoratorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var LatteDecorator
     */
    private $latteDecorator;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    protected function setUp(): void
    {
        $this->latteDecorator = $this->container->get(LatteDecorator::class);
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
        $fileInfo = new SplFileInfo(__DIR__ . '/LatteDecoratorSource/fileWithoutLayout.latte');
        $file = $this->fileFactory->create($fileInfo);
        $this->latteDecorator->decorateFile($file);

        $this->assertContains('Contact me!', $file->getContent());
    }

    public function testDecorateFileWithLayout(): void
    {
        $fileInfo = new SplFileInfo(__DIR__ . '/LatteDecoratorSource/contact.latte');
        $file = $this->fileFactory->create($fileInfo);
        $file->setConfiguration([
            'layout' => 'default',
        ]);

        $this->latteDecorator->decorateFile($file);

        $this->assertContains('This is layout!', $file->getContent());
        $this->assertContains('Contact us!', $file->getContent());
    }

    public function testDecorateFileWithFileVariable(): void
    {
        $fileInfo = new SplFileInfo(__DIR__ . '/LatteDecoratorSource/fileWithFileVariable.latte');
        $file = $this->fileFactory->create($fileInfo);
        $this->latteDecorator->decorateFile($file);

        $this->assertContains('fileWithFileVariable.latte', $file->getContent());
    }
}
