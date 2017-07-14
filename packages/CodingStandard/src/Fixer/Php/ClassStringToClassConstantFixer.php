<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Php;

use Nette\Utils\Strings;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class ClassStringToClassConstantFixer implements DefinedFixerInterface
{

    /**
     * @var string
     */
    private const CLASS_OR_INTERFACE_PATTERN = '#^[A-Z]\w*[a-z]\w*(\\\\[A-Z]\w*[a-z]\w*)*\z#';

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            '"SomeClass::class" references should be used over string.',
            [
                new CodeSample(
'<?php      

$className = "DateTime";  
                '),
                new CodeSample(
'<?php      

$interfaceName = "DateTimeInterface";  
                '),
                new CodeSample(
'<?php      

$interfaceName = "Nette\Utils\DateTime";  
                '),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_CONSTANT_ENCAPSED_STRING);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        /** @var Token[] $revertedTokens */
        $revertedTokens = array_reverse($tokens->toArray(), true);

        foreach ($revertedTokens as $index => $token) {
            if (! $token->isGivenKind(T_CONSTANT_ENCAPSED_STRING)) {
                continue;
            }

            $potentialClassOrInterface = trim($token->getContent(), "'");
            if ($this->isClassOrInterface($potentialClassOrInterface)) {
                $token->clear(); // overrideAt() fails on "Illegal offset type"

                $classOrInterfaceTokens = $this->convertClassOrInterfaceNameToTokens($potentialClassOrInterface);
                $tokens->insertAt($index, array_merge($classOrInterfaceTokens, [
                    new Token([T_DOUBLE_COLON, '::']),
                    new Token([CT::T_CLASS_CONSTANT, 'class']),
                ]));
            }
        }
    }

    public function isRisky(): bool
    {
        return false;
    }

    public function getName(): string
    {
        return self::class;
    }

    public function getPriority(): int
    {
        // @todo combine with namespace import fixer/sniff
        return 0;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }

    private function isClassOrInterface(string $potentialClassOrInterface): bool
    {
        return (bool) preg_match(self::CLASS_OR_INTERFACE_PATTERN, $potentialClassOrInterface);
    }

    /**
     * @return Token[]
     */
    private function convertClassOrInterfaceNameToTokens(string $potentialClassOrInterface): array
    {
        $tokens = [];
        $nameParts = explode('\\', $potentialClassOrInterface);

        foreach ($nameParts as $namePart) {
            $tokens[] = new Token([T_NS_SEPARATOR, '\\']);
            $tokens[] = new Token([T_STRING, $namePart]);
        }

        return $tokens;
    }
}
