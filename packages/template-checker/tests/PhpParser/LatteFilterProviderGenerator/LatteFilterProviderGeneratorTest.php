<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\Tests\PhpParser\LatteFilterProviderGenerator;

use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\TemplateChecker\HttpKernel\TemplateCheckerKernel;
use Symplify\TemplateChecker\PhpParser\LatteFilterProviderFactory;
use Symplify\TemplateChecker\Tests\PhpParser\LatteFilterProviderGenerator\Source\SomeHelper;
use Symplify\TemplateChecker\ValueObject\ClassMethodName;

final class LatteFilterProviderGeneratorTest extends AbstractKernelTestCase
{
    /**
     * @var LatteFilterProviderFactory
     */
    private $latteFilterProviderGenerator;

    protected function setUp(): void
    {
        self::bootKernel(TemplateCheckerKernel::class);
        $this->latteFilterProviderGenerator = self::$container->get(LatteFilterProviderFactory::class);
    }

    public function test(): void
    {
        $classMethodName = new ClassMethodName(SomeHelper::class, 'someMethod', new SmartFileInfo(
            __DIR__ . '/Source/SomeHelper.php'
        ));

        $generatedContent = $this->latteFilterProviderGenerator->createFromClassMethodName($classMethodName);
        $this->assertStringEqualsFile(__DIR__ . '/Fixture/expected_filter_provider.php.inc', $generatedContent);
    }
}
