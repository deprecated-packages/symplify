<?php

declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable\Latte;

use Latte\Engine;
use Latte\ILoader;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Configuration\Parser\NeonParser;
use Symplify\Statie\Renderable\File\File;
use Symplify\Statie\Renderable\Latte\DynamicStringLoader;
use Symplify\Statie\Renderable\Latte\LatteDecorator;

final class LatteDecoratorTest extends TestCase
{
    /**
     * @var LatteDecorator
     */
    private $latteDecorator;

    protected function setUp()
    {
        $stringLoader = $this->createStringLoader();

        $this->latteDecorator = new LatteDecorator(
            new Configuration(new NeonParser()),
            $this->createLatteEngine($stringLoader),
            $stringLoader
        );
    }

    public function testDecorateFile()
    {
        $fileInfo = new SplFileInfo(__DIR__ . '/LatteDecoratorSource/fileWithoutLayout.latte');
        $file = new File($fileInfo, 'fileWithoutLayout');
        $this->latteDecorator->decorateFile($file);

        $this->assertContains('Contact me!', $file->getContent());
    }

    public function testDecorateFileWithLayout()
    {
        $fileInfo = new SplFileInfo(__DIR__ . '/LatteDecoratorSource/contact.latte');
        $file = new File($fileInfo, 'contact.latte');
        $file->setConfiguration([
            'layout' => 'default',
        ]);

        $this->latteDecorator->decorateFile($file);

        $this->assertContains('This is layout!', $file->getContent());
        $this->assertContains('Contact us!', $file->getContent());
    }

    private function createLatteEngine(ILoader $loader) : Engine
    {
        $latte = new Engine();
        $latte->setLoader($loader);

        return $latte;
    }

    private function createStringLoader() : DynamicStringLoader
    {
        $loader = new DynamicStringLoader();
        $loader->addTemplate(
            'default',
            file_get_contents(__DIR__ . '/LatteDecoratorSource/default.latte')
        );

        return $loader;
    }
}
