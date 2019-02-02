<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Tests\Wrapper\FixerWrapper\ClassWrapper;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Tests\AbstractContainerAwareTestCase;
use Symplify\TokenRunner\Tests\Wrapper\FixerWrapper\ClassWrapper\Source\AbstractClass;
use Symplify\TokenRunner\Tests\Wrapper\FixerWrapper\ClassWrapper\Source\SomeClass;
use Symplify\TokenRunner\Tests\Wrapper\FixerWrapper\ClassWrapper\Source\SomeInterface;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ClassWrapper;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ClassWrapperFactory;

final class ClassWrapperTest extends AbstractContainerAwareTestCase
{
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

    private function createClassWrapperFromFile(string $filePath): ClassWrapper
    {
        $classWrapperFactory = $this->container->get(ClassWrapperFactory::class);

        $tokens = Tokens::fromCode(file_get_contents($filePath));
        $classTokens = $tokens->findGivenKind([T_CLASS], 0);
        $classTokenPosition = key(array_pop($classTokens));

        return $classWrapperFactory->createFromTokensArrayStartPosition($tokens, $classTokenPosition);
    }
}
