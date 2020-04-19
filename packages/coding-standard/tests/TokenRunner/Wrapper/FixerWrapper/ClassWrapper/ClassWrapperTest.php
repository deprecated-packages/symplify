<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\TokenRunner\Wrapper\FixerWrapper\ClassWrapper;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\Tests\HttpKernel\SymplifyCodingStandardKernel;
use Symplify\CodingStandard\Tests\TokenRunner\Wrapper\FixerWrapper\ClassWrapper\Source\AbstractClass;
use Symplify\CodingStandard\Tests\TokenRunner\Wrapper\FixerWrapper\ClassWrapper\Source\SomeClass;
use Symplify\CodingStandard\Tests\TokenRunner\Wrapper\FixerWrapper\ClassWrapper\Source\SomeInterface;
use Symplify\CodingStandard\TokenRunner\ValueObject\Wrapper\FixerWrapper\FixerClassWrapper;
use Symplify\CodingStandard\TokenRunner\Wrapper\FixerWrapper\FixerClassWrapperFactory;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class ClassWrapperTest extends AbstractKernelTestCase
{
    /**
     * @var FixerClassWrapperFactory
     */
    private $fixerClassWrapperFactory;

    protected function setUp(): void
    {
        $this->bootKernel(SymplifyCodingStandardKernel::class);
        $this->fixerClassWrapperFactory = self::$container->get(FixerClassWrapperFactory::class);
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
        $tokens = Tokens::fromCode(file_get_contents($filePath));
        $classTokens = $tokens->findGivenKind([T_CLASS], 0);
        $classTokenPosition = key(array_pop($classTokens));

        return $this->fixerClassWrapperFactory->createFromTokensArrayStartPosition($tokens, $classTokenPosition);
    }
}
