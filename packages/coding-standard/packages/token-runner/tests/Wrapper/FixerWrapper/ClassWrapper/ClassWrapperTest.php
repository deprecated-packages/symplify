<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Tests\Wrapper\FixerWrapper\ClassWrapper;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Tests\HttpKernel\TokenRunnerKernel;
use Symplify\CodingStandard\TokenRunner\Tests\Wrapper\FixerWrapper\ClassWrapper\Source\AbstractClass;
use Symplify\CodingStandard\TokenRunner\Tests\Wrapper\FixerWrapper\ClassWrapper\Source\SomeClass;
use Symplify\CodingStandard\TokenRunner\Tests\Wrapper\FixerWrapper\ClassWrapper\Source\SomeInterface;
use Symplify\CodingStandard\TokenRunner\ValueObject\Wrapper\FixerWrapper\FixerClassWrapper;
use Symplify\CodingStandard\TokenRunner\Wrapper\FixerWrapper\FixerClassWrapperFactory;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class ClassWrapperTest extends AbstractKernelTestCase
{
    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(TokenRunnerKernel::class, [__DIR__ . '/../../../config/config_tests.yaml']);
    }

    public function testGetNames(): void
    {
        $classWrapper = $this->createClassWrapperFromFile(__DIR__ . '/Source/SomeClass.php');

        $this->assertSame(SomeClass::class, $classWrapper->getClassName());
        $this->assertSame(AbstractClass::class, $classWrapper->getParentClassName());

        $this->assertSame(
            [SomeClass::class, AbstractClass::class, SomeInterface::class],
            $classWrapper->getClassTypes()
        );
    }

    public function testGetClassTypesWithoutParentClass(): void
    {
        $classWrapper = $this->createClassWrapperFromFile(__DIR__ . '/Source/ContainerFactory.php');

        $this->assertSame([], $classWrapper->getClassTypes());
    }

    private function createClassWrapperFromFile(string $filePath): FixerClassWrapper
    {
        $classWrapperFactory = self::$container->get(FixerClassWrapperFactory::class);

        $tokens = Tokens::fromCode(file_get_contents($filePath));
        $classTokens = $tokens->findGivenKind([T_CLASS], 0);
        $classTokenPosition = key(array_pop($classTokens));

        return $classWrapperFactory->createFromTokensArrayStartPosition($tokens, $classTokenPosition);
    }
}
