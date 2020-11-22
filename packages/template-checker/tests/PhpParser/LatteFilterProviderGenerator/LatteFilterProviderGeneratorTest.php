<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\Tests\PhpParser\LatteFilterProviderGenerator;

use Symplify\TemplateChecker\HttpKernel\TemplateCheckerKernel;
use Symplify\TemplateChecker\PhpParser\LatteFilterProviderGenerator;
use Symplify\TemplateChecker\Tests\PhpParser\LatteFilterProviderGenerator\Source\SomeHelper;
use Symplify\TemplateChecker\ValueObject\ClassMethodName;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class LatteFilterProviderGeneratorTest extends AbstractKernelTestCase
{
    /**
     * @var LatteFilterProviderGenerator
     */
    private $latteFilterProviderGenerator;

    protected function setUp(): void
    {
        self::bootKernel(TemplateCheckerKernel::class);
        $this->latteFilterProviderGenerator = self::$container->get(LatteFilterProviderGenerator::class);
    }

    public function test(): void
    {
        $classMethodName = new ClassMethodName(SomeHelper::class, 'someMethod', new SmartFileInfo(
            __DIR__ . '/Source/SomeHelper.php'
        ));

        $generatedContent = $this->latteFilterProviderGenerator->generate($classMethodName);
        $this->assertStringEqualsFile(__DIR__ . '/Fixture/expected_filter_provider.php.inc', $generatedContent);
    }
}
