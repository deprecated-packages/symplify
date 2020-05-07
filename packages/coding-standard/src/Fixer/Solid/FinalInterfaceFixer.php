<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Solid;

use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\TokenRunner\ValueObject\Wrapper\FixerWrapper\FixerClassWrapper;
use Symplify\CodingStandard\TokenRunner\Wrapper\FixerWrapper\FixerClassWrapperFactory;

/**
 * @deprecated
 */
final class FinalInterfaceFixer extends AbstractSymplifyFixer implements ConfigurableFixerInterface
{
    /**
     * @var string[]
     */
    private $onlyInterfaces = [];

    /**
     * @var FixerClassWrapperFactory
     */
    private $fixerClassWrapperFactory;

    public function __construct(FixerClassWrapperFactory $fixerClassWrapperFactory)
    {
        $this->fixerClassWrapperFactory = $fixerClassWrapperFactory;

        trigger_error(sprintf(
            'Fixer "%s" is deprecated and will be removed in Symplify 8 (May 2020). Use more advanced "%s" instead',
            self::class,
            'https://github.com/rectorphp/rector/blob/master/docs/AllRectorsOverview.md#finalizeclasseswithoutchildrenrector'
        ));

        sleep(3);
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
            $classWrapper = $this->fixerClassWrapperFactory->createFromTokensArrayStartPosition($tokens, $index);
            if ($this->shouldBeSkipped($classWrapper)) {
                continue;
            }

            $finalWithSpaceTokens = Tokens::fromArray([
                new Token([T_FINAL, 'final']),
                new Token([T_WHITESPACE, ' ']),
            ]);

            $tokens->insertAt($index, $finalWithSpaceTokens);
        }
    }

    /**
     * @param mixed[]|null $configuration
     */
    public function configure(?array $configuration = null): void
    {
        $this->onlyInterfaces = $configuration['only_interfaces'] ?? [];
    }

    private function shouldBeSkipped(FixerClassWrapper $fixerClassWrapper): bool
    {
        if (! $fixerClassWrapper->doesImplementInterface()) {
            return true;
        }

        if ($fixerClassWrapper->isFinal() || $fixerClassWrapper->isAbstract()) {
            return true;
        }

        if ($fixerClassWrapper->isDoctrineEntity()) {
            return true;
        }

        if ($fixerClassWrapper->isAnonymous()) {
            return true;
        }

        if ($this->onlyInterfaces !== []) {
            return ! array_intersect($this->onlyInterfaces, $fixerClassWrapper->getInterfaceNames());
        }

        return false;
    }
}
