<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Solid;

use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ClassWrapper;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ClassWrapperFactory;

final class FinalInterfaceFixer extends AbstractSymplifyFixer implements ConfigurableFixerInterface
{
    /**
     * @var string[]
     */
    private $onlyInterfaces = [];

    /**
     * @var ClassWrapperFactory
     */
    private $classWrapperFactory;

    public function __construct(ClassWrapperFactory $classWrapperFactory)
    {
        $this->classWrapperFactory = $classWrapperFactory;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Non-abstract class that implements interface should be final.',
            [new CodeSample('
<?php
class SomeClass implements SomeInterface {};')]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_CLASS, T_STRING, T_IMPLEMENTS, '{', '}'])
            && ! $tokens->isTokenKindFound(T_FINAL);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($this->getReversedClassyPositions($tokens) as $index) {
            $classWrapper = $this->classWrapperFactory->createFromTokensArrayStartPosition($tokens, $index);
            if ($this->shouldBeSkipped($classWrapper)) {
                continue;
            }

            $tokens->insertAt($index, [new Token([T_FINAL, 'final']), new Token([T_WHITESPACE, ' '])]);
        }
    }

    /**
     * @param mixed[]|null $configuration
     */
    public function configure(?array $configuration = null): void
    {
        $this->onlyInterfaces = $configuration['only_interfaces'] ?? [];
    }

    private function shouldBeSkipped(ClassWrapper $classWrapper): bool
    {
        if (! $classWrapper->implementsInterface()) {
            return true;
        }

        if ($classWrapper->isFinal() || $classWrapper->isAbstract()) {
            return true;
        }

        if ($classWrapper->isDoctrineEntity()) {
            return true;
        }

        if ($classWrapper->isAnonymous()) {
            return true;
        }

        if ($this->onlyInterfaces) {
            return ! array_intersect($this->onlyInterfaces, $classWrapper->getInterfaceNames());
        }

        return false;
    }
}
