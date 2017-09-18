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
use Symplify\CodingStandard\Tokenizer\ClassTokensAnalyzer;

final class PropertyNameMatchingTypeFixer extends AbstractFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Property name should match its type, if possible.', [
            new CodeSample(
                '<?php
class SomeClass
{
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }
}'
            ),
        ]);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_STRING)
            && $tokens->isAnyTokenKindsFound(Token::getClassyTokenKinds());
    }

    protected function applyFix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (! $token->isClassy()) {
                continue;
            }

            $classTokenAnalyzer = ClassTokensAnalyzer::createFromTokensArrayStartPosition($tokens, $index);

            dump($classTokenAnalyzer);
            die;

            dump($token);
            die;


            // process properties
            // process method

            // collect renames? => rename

            // var?
            dump($token);
//            die;
//
//            $methodNamePosition = (int) $tokens->getNextMeaningfulToken($index);
//            $methodNameToken = $tokens[$methodNamePosition];
//
//            if (! $this->isMethodNameCandidate($methodNameToken)) {
//                continue;
//            }
//
//            $correctName = $this->getCorrectedNameIfNeeded($methodNameToken->getContent());
//            if ($correctName === false) {
//                continue;
//            }
//
//            $this->fixMethodName($tokens, $correctName, $methodNamePosition);
        }
    }

    private function isMethodNameCandidate(Token $methodNameToken): bool
    {
        if (! $methodNameToken->isGivenKind(T_STRING)) {
            // expected next token is not a method name, not our match
            return false;
        }

        if (! Strings::startsWith($methodNameToken->getContent(), '__')) {
            // not PHP internal method
            return false;
        }

        return true;
    }

    private function fixVariableOrPropertyName(Tokens $tokens, string $correctName, int $methodNamePosition): void
    {
        $tokens[$methodNamePosition] = new Token([T_STRING, $correctName]);
    }
}
