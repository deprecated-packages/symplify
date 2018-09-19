<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Tests\Wrapper\FixerWrapper\ClassWrapper;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Tests\AbstractContainerAwareTestCase;
use Symplify\TokenRunner\Tests\Wrapper\FixerWrapper\ClassWrapper\Source\AbstractClass;
use Symplify\TokenRunner\Tests\Wrapper\FixerWrapper\ClassWrapper\Source\SomeClass;
use Symplify\TokenRunner\Tests\Wrapper\FixerWrapper\ClassWrapper\Source\SomeInterface;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ClassWrapper;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ClassWrapperFactory;
use function Safe\file_get_contents;

final class ClassWrapperTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ClassWrapperFactory
     */
    private $classWrapperFactory;

    /**
     * @var ClassWrapper
     */
    private $classWrapper;

    protected function setUp(): void
    {
        $this->classWrapperFactory = $this->container->get(ClassWrapperFactory::class);

        $tokens = Tokens::fromCode(file_get_contents(__DIR__ . '/Source/SomeClass.php'));
        $classTokens = $tokens->findGivenKind([T_CLASS], 0);
        $classTokenPosition = key(array_pop($classTokens));

        $this->classWrapper = $this->classWrapperFactory->createFromTokensArrayStartPosition(
            $tokens,
            $classTokenPosition
        );
    }

    public function testGetNames(): void
    {
        $this->assertSame(SomeClass::class, $this->classWrapper->getClassName());
        $this->assertSame(AbstractClass::class, $this->classWrapper->getParentClassName());
    }

    public function testGetClassTypes(): void
    {
        $this->assertSame([
            SomeClass::class,
            AbstractClass::class,
            SomeInterface::class,
        ], $this->classWrapper->getClassTypes());
    }
}
