<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Php;

use Nette\Utils\Strings;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\PackageBuilder\Types\ClassLikeExistenceChecker;

/**
 * @deprecated
 */
final class ClassStringToClassConstantFixer extends AbstractSymplifyFixer implements ConfigurableFixerInterface
{
    /**
     * @var string
     */
    private const CLASS_LIKE_REGEX = '#^[\\\\]?[A-Z]\w*(\\\\[A-Z]\w*)*$#';

    /**
     * @var bool
     */
    private $classMustExists = true;

    /**
     * Classes allowed to be in string format
     *
     * @var string[]
     */
    private $allowClasses = [];

    /**
     * @var ClassLikeExistenceChecker
     */
    private $classLikeExistenceChecker;

    public function __construct(ClassLikeExistenceChecker $classLikeExistenceChecker)
    {
        $this->classLikeExistenceChecker = $classLikeExistenceChecker;

        trigger_error(sprintf(
            'Fixer "%s" is deprecated. Use "%s" instead',
            self::class,
            'https://github.com/rectorphp/rector/blob/master/docs/rector_rules_overview.md#stringclassnametoclassconstantrector'
        ));

        sleep(3);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            '`::class` references should be used over string for classes and interfaces.',
            [
                new CodeSample('<?php $className = "DateTime";'),
                new CodeSample('<?php $interfaceName = "DateTimeInterface";'),
                new CodeSample('<?php $interfaceName = "Nette\Utils\DateTime";'),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_CONSTANT_ENCAPSED_STRING);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($this->reverseTokens($tokens) as $index => $token) {
            if (! $token->isGivenKind(T_CONSTANT_ENCAPSED_STRING)) {
                continue;
            }

            $potentialClassInterfaceOrTrait = $this->getNameFromToken($token);
            $potentialClassInterfaceOrTrait = $this->normalizeClassyName($potentialClassInterfaceOrTrait);
            if (! $this->isValidClassLike($potentialClassInterfaceOrTrait)) {
                continue;
            }

            unset($tokens[$index]);
            $tokens->insertAt($index, $this->convertNameToTokens($potentialClassInterfaceOrTrait));
        }
    }

    /**
     * @param mixed[]|null $configuration
     */
    public function configure(?array $configuration = null): void
    {
        $this->classMustExists = $configuration['class_must_exist'] ?? true;
        $this->allowClasses = $configuration['allow_classes'] ?? [];
    }

    private function getNameFromToken(Token $token): string
    {
        // remove quotes "" around the string
        $name = Strings::substring($token->getContent(), 1, -1);

        // remove "\" prefix
        return ltrim($name, '\\');
    }

    private function normalizeClassyName(string $classyName): string
    {
        return str_replace('\\\\', '\\', $classyName);
    }

    private function isValidClassLike(string $classLike): bool
    {
        if ($classLike === '') {
            return false;
        }

        // lowercase string are not classes; required because class_exists() is case-insensitive
        if (ctype_lower($classLike[0])) {
            return false;
        }

        foreach ($this->allowClasses as $allowedClass) {
            if ($classLike === $allowedClass) {
                return false;
            }
        }

        if (! (bool) Strings::match($classLike, self::CLASS_LIKE_REGEX)) {
            return false;
        }

        if (! $this->classMustExists) {
            return true;
        }

        return $this->classLikeExistenceChecker->exists($classLike);
    }

    private function convertNameToTokens(string $classInterfaceOrTraitName): Tokens
    {
        $tokens = [];

        $parts = explode('\\', $classInterfaceOrTraitName);
        foreach ($parts as $part) {
            $tokens[] = new Token([T_NS_SEPARATOR, '\\']);
            $tokens[] = new Token([T_STRING, $part]);
        }

        $tokens[] = new Token([T_DOUBLE_COLON, '::']);
        $tokens[] = new Token([CT::T_CLASS_CONSTANT, 'class']);

        return Tokens::fromArray($tokens);
    }
}
