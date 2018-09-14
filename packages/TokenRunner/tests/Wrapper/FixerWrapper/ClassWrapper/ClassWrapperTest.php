<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Tests\Wrapper\FixerWrapper\ClassWrapper;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Tests\AbstractContainerAwareTestCase;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ClassWrapperFactory;
use function Safe\file_get_contents;

final class ClassWrapperTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ClassWrapperFactory
     */
    private $classWrapperFactory;

    protected function setUp(): void
    {
        $this->classWrapperFactory = $this->container->get(ClassWrapperFactory::class);
    }

    public function testGet(): void
    {
        $tokens = Tokens::fromCode(file_get_contents(__DIR__ . '/Source/SomeClass.php'));
        $classTokens = $tokens->findGivenKind([T_CLASS], 0);

        $classTokenPosition = key(array_pop($classTokens));
        $classWrapper = $this->classWrapperFactory->createFromTokensArrayStartPosition($tokens, $classTokenPosition);

        $this->assertSame('Rector\Rector\AbstractRector', $classWrapper->getParentClassName());
    }
}
