<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\PhpParser\LatteFilterProviderGenerator;

use Symplify\EasyCI\HttpKernel\EasyCIKernel;
use Symplify\EasyCI\PhpParser\LatteFilterProviderFactory;
use Symplify\EasyCI\Tests\PhpParser\LatteFilterProviderGenerator\Source\SomeHelper;
use Symplify\EasyCI\ValueObject\ClassMethodName;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class LatteFilterProviderGeneratorTest extends AbstractKernelTestCase
{
    /**
     * @var LatteFilterProviderFactory
     */
    private $latteFilterProviderFactory;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->latteFilterProviderFactory = $this->getService(LatteFilterProviderFactory::class);
    }

    public function test(): void
    {
        $classMethodName = new ClassMethodName(SomeHelper::class, 'someMethod', new SmartFileInfo(
            __DIR__ . '/Source/SomeHelper.php'
        ));

        $generatedContent = $this->latteFilterProviderFactory->createFromClassMethodName($classMethodName);
        $this->assertStringEqualsFile(__DIR__ . '/Fixture/expected_filter_provider.php.inc', $generatedContent);
    }
}
