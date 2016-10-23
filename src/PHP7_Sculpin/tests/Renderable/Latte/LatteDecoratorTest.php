<?php

declare(strict_types=1);

namespace Symplify\PHP7_Sculpin\Tests\Renderable\Latte;

use Latte\Engine;
use Latte\ILoader;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\PHP7_Sculpin\Configuration\Configuration;
use Symplify\PHP7_Sculpin\Configuration\Parser\YamlAndNeonParser;
use Symplify\PHP7_Sculpin\Renderable\File\File;
use Symplify\PHP7_Sculpin\Renderable\Latte\DynamicStringLoader;
use Symplify\PHP7_Sculpin\Renderable\Latte\LatteDecorator;

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
            new Configuration(new YamlAndNeonParser()),
            $this->createLatteEngine($stringLoader),
            $stringLoader
        );
    }

    public function testDecorateFile()
    {
        $fileInfo = new SplFileInfo(__DIR__.'/LatteDecoratorSource/contact.latte');
        $file = new File($fileInfo, 'contact.latte');
        $this->latteDecorator->decorateFile($file);

        $this->assertContains('This is layout!', $file->getContent());
        $this->assertContains('Contact us!', $file->getContent());
    }

    public function testDecorateFileWithAnotherLayout()
    {
        $fileInfo = new SplFileInfo(__DIR__.'/LatteDecoratorSource/contactWithAnotherLayout.latte');
        $file = new File($fileInfo, 'contactWithAnotherLayout.latte');
        $file->setConfiguration([
            'layout' => 'default',
        ]);

        $this->latteDecorator->decorateFile($file);

        $this->assertContains('This is layout!', $file->getContent());
        $this->assertContains('Contact us with another layout!', $file->getContent());
    }

    public function testDecorateFileWithLayout()
    {
        $fileInfo = new SplFileInfo(__DIR__.'/LatteDecoratorSource/fileWithLayout.latte');
        $file = new File($fileInfo, 'fileWithLayout.latte');
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
            file_get_contents(__DIR__.'/LatteDecoratorSource/@default.latte')
        );

        $loader->addTemplate(
            'anotherLayout',
            file_get_contents(__DIR__.'/LatteDecoratorSource/@anotherLayout.latte')
        );

        return $loader;
    }
}
