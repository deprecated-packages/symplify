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
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            '::class refences should be used over string.',
            [
                new CodeSample(
'<?php      

$className = "DateTime";  
                '),
                new CodeSample(
'<?php      

$interfaceName = "DateTimeInterface";  
                '),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return true;
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach (array_reverse($tokens->toArray(), true) as $index => $token) {
            /** @var Token $token */
            if (! $this->isStringToken($token)) {
                continue;
            }

            $potentialClassOrInterfaceName = trim($token->getContent(), "'");
            if (class_exists($potentialClassOrInterfaceName) || interface_exists($potentialClassOrInterfaceName)) {
                $token->clear(); // overrideAt() fails on "Illegal offset type"
                $tokens->insertAt($index, [
                    new Token([T_NS_SEPARATOR, '\\']),
                    new Token([T_STRING, $potentialClassOrInterfaceName]),
                    new Token([T_DOUBLE_COLON, '::']),
                    new Token([CT::T_CLASS_CONSTANT, 'class']),
                ]);
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

    private function isStringToken(Token $token): bool
    {
        return Strings::startsWith($token->getContent(), "'")
            && Strings::endsWith($token->getContent(), "'");
    }
}
