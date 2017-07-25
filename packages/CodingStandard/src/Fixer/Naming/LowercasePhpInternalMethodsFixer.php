<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Naming;

use Nette\Utils\Strings;
use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class LowercasePhpInternalMethodsFixer extends AbstractFixer
{
    /**
     * @var string[]
     */
    private $phpMethods = [
        '__construct',
        '__destruct',
        '__call',
        '__get',
        '__set',
        '__isset',
        '__unset',
        '__sleep',
        '__wakeup',
        '__invoke',
        '__set_state',
        '__clone()',
        // todo: not lowercased
        // '__callStatic',
        // '__toString',
        // '__debugInfo()',
    ];

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Internal PHP methods should be lowercased.',
            [
                new CodeSample(
                    '<?php
class SomeClass
{
    public function __CONSTRUCT()
    {
    }
}'),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_FUNCTION, T_STRING])
            && $tokens->isAnyTokenKindsFound(Token::getClassyTokenKinds());
    }

    protected function applyFix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (! $token->isGivenKind(T_FUNCTION)) {
                continue;
            }

            $methodNamePosition = $tokens->getNextMeaningfulToken($index);
            $methodNameToken = $tokens[$methodNamePosition];

            if (! $methodNameToken->isGivenKind(T_STRING)) {
                // expected next token is not a method name, not our match
                continue;
            }

            if (! Strings::startsWith($methodNameToken->getContent(), '__')) {
                // not PHP internal method
                continue;
            }

            $methodName = $methodNameToken->getContent();
            $lowercasedMethodName = strtolower($methodName);
            if ($methodName === $lowercasedMethodName) {
                continue;
            }

            if (! in_array($lowercasedMethodName, $this->phpMethods)) {
                continue;
            }

            $tokens[$methodNamePosition] = new Token([T_STRING, $lowercasedMethodName]);
        }
    }
}
