<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Solid;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ClassWrapper;

final class FinalInterfaceFixer implements FixerInterface, DefinedFixerInterface
{
    /**
     * Optional
     *
     * @var string[]
     */
    public $onlyInterfaces = ['EventSubscriber'];

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Non-abstract class that implements interface should be final.',
            [
                new CodeSample('
<?php
class SomeClass implements SomeInterface {};'),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_CLASS, T_IMPLEMENTS])
            && ! $tokens->isTokenKindFound(T_FINAL);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (! $token->isGivenKind(T_CLASS)) {
                continue;
            }

            $classWrapper = ClassWrapper::createFromTokensArrayStartPosition($tokens, $index);
            if (! $classWrapper->implementsInterface()) {
                return;
            }

            if ($classWrapper->isFinal() || $classWrapper->isAbstract()) {
                return;
            }

            if ($classWrapper->isDoctrineEntity()) {
                return;
            }

            $this->fixClass($tokens, $index);
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
        return 0;
    }

    public function supports(SplFileInfo $file)
    {
        return true;
    }

    private function fixClass(Tokens $tokens, int $position): void
    {
        $tokens->insertAt($position, [
            new Token([T_FINAL, 'final']),
            new Token([T_WHITESPACE, ' '])
        ]);
    }

    private function shouldBeSkipped(): bool
    {
//        if ($this->onlyInterfaces) {
//            $interfacePosition = $this->file->findNext(T_IMPLEMENTS, $this->position + 2);$interfacePosition =                          nameStart = $this->file->findNext(T_IMPLEMENTS, $interfacePosition);
//            // ...
//            $name = NameFactory::createFromFileAndStart($this->file, $interfacePosition);
//            dump($name);
//            die;
//
////            NameFactory::createFromTokensAndStart('...');
//        }

        return false;
    }
}
