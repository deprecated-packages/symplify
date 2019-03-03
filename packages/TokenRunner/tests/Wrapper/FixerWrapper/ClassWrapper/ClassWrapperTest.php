<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Tests\Wrapper\FixerWrapper\ClassWrapper;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\TokenRunner\Tests\HttpKernel\TokenRunnerKernel;
use Symplify\TokenRunner\Tests\Wrapper\FixerWrapper\ClassWrapper\Source\AbstractClass;
use Symplify\TokenRunner\Tests\Wrapper\FixerWrapper\ClassWrapper\Source\SomeClass;
use Symplify\TokenRunner\Tests\Wrapper\FixerWrapper\ClassWrapper\Source\SomeInterface;
use Symplify\TokenRunner\Wrapper\FixerWrapper\FixerClassWrapper;
use Symplify\TokenRunner\Wrapper\FixerWrapper\FixerClassWrapperFactory;

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
