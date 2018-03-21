<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Import;

use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Object_;
use SplFileInfo;
use Symplify\BetterReflectionDocBlock\Tag\TolerantParam;
use Symplify\BetterReflectionDocBlock\Tag\TolerantReturn;
use Symplify\PackageBuilder\Reflection\PrivatesSetter;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\ClassNameFinder;
use Symplify\TokenRunner\Naming\Name\Name;
use Symplify\TokenRunner\Naming\Name\NameAnalyzer;
use Symplify\TokenRunner\Naming\Name\NameFactory;
use Symplify\TokenRunner\Transformer\FixerTransformer\UseImportsTransformer;
use Symplify\TokenRunner\Wrapper\FixerWrapper\DocBlockWrapper;
use Symplify\TokenRunner\Wrapper\FixerWrapper\DocBlockWrapperFactory;

/**
 * Possible cases:
 *
 * - 1) string that start with pre slash \SomeThing
 * - 2) namespace with conflicts \First\SomeClass + \Second\SomeClass
 * - 3) partial namespaces \Namespace\Partial + Partial\Class
 */
final class ImportNamespacedNameFixer implements DefinedFixerInterface, ConfigurationDefinitionFixerInterface, WhitespacesAwareFixerInterface
{
    /**
     * @var string
     */
    public const ALLOW_SINGLE_NAMES_OPTION = 'allow_single_names';

    /**
     * @var string
     */
    public const INCLUDE_DOC_BLOCKS_OPTION = 'include_doc_blocks';

    /**
     * @var NamespaceUseAnalysis[]
     */
    private $namespaceUseAnalyses = [];

    /**
     * @var mixed[]
     */
    private $configuration = [];

    /**
     * @var WhitespacesFixerConfig
     */
    private $whitespacesFixerConfig;

    /**
     * @var Name[]
     */
    private $newUseStatementNames = [];

    /**
     * @var DocBlockWrapperFactory
     */
    private $docBlockWrapperFactory;

    public function __construct(DocBlockWrapperFactory $docBlockWrapperFactory)
    {
        $this->docBlockWrapperFactory = $docBlockWrapperFactory;

        // set defaults
        $this->configuration = $this->getConfigurationDefinition()
            ->resolve([]);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Types should not be referenced via a fully/partially qualified name, but via a use statement.',
            [
                new CodeSample('<?php $value = \SomeNamespace\SomeClass'),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_CLASS) && $tokens->isAnyTokenKindsFound([T_DOC_COMMENT, T_STRING]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        $this->namespaceUseAnalyses = (new NamespaceUsesAnalyzer())->getDeclarationsFromTokens($tokens);
        $this->newUseStatementNames = [];

        for ($index = $tokens->getSize() - 1; $index > 0; --$index) {
            $token = $tokens[$index];

            // class name is same as token that could be imported, skip
            if ($token->getContent() === ClassNameFinder::findInTokens($tokens)) {
                continue;
            }

            if ($token->isGivenKind(T_DOC_COMMENT)) {
                if (! $this->configuration[self::INCLUDE_DOC_BLOCKS_OPTION]) {
                    continue;
                }

                $this->processDocCommentToken($index, $tokens);
                continue;
            }

            if ($token->isGivenKind(T_STRING)) {
                $this->processStringToken($token, $index, $tokens);
                continue;
            }
        }

        UseImportsTransformer::addNamesToTokens($this->newUseStatementNames, $tokens);
    }

    /**
     * Run before:
     * - @see \PhpCsFixer\Fixer\Import\OrderedImportsFixer
     * - @see \PhpCsFixer\Fixer\Import\SingleLineAfterImportsFixer
     */
    public function getPriority(): int
    {
        return 10;
    }

    /**
     * @param mixed[]|null $configuration
     */
    public function configure(?array $configuration = null): void
    {
        if ($configuration === null) {
            return;
        }

        $this->configuration = $this->getConfigurationDefinition()
            ->resolve($configuration);
    }

    public function getConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        $allowSingleNamesOption = (new FixerOptionBuilder(
            self::ALLOW_SINGLE_NAMES_OPTION,
            'Whether allow \SingleClassName or import it.'
        ))->setAllowedValues([true, false])
            ->setDefault(false)
            ->getOption();

        $includeDocBlocksOption = (new FixerOptionBuilder(
            self::INCLUDE_DOC_BLOCKS_OPTION,
            'Whether to include importing from doc blocks.'
        ))->setAllowedValues([true, false])
            ->setDefault(false)
            ->getOption();

        return new FixerConfigurationResolver([$allowSingleNamesOption, $includeDocBlocksOption]);
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }

    /**
     * There are still some edge cases to be found and improve.
     */
    public function isRisky(): bool
    {
        return true;
    }

    public function getName(): string
    {
        return self::class;
    }

    public function setWhitespacesConfig(WhitespacesFixerConfig $whitespacesFixerConfig): void
    {
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
    }

    /**
     * Prefix duplicate class names with vendor name
     */
    private function uniquateLastPart(Name $name): Name
    {
        foreach ($this->namespaceUseAnalyses as $namespaceUseAnalysis) {
            if ($namespaceUseAnalysis->getShortName() === $name->getLastName() && $namespaceUseAnalysis->getFullName() !== $name->getName()) {
                $uniquePrefix = $name->getFirstName();
                $name->addAlias($uniquePrefix . $name->getLastName());
                return $name;
            }
        }

        foreach ($this->newUseStatementNames as $newUseStatementName) {
            if ($this->shouldBeUniquated($newUseStatementName, $name)) {
                $uniquePrefix = $name->getFirstName();
                $name->addAlias($uniquePrefix . $name->getLastName());
                return $name;
            }
        }

        return $name;
    }

    private function processStringToken(Token $token, int $index, Tokens $tokens): void
    {
        // Case 1.
        if (! NameAnalyzer::isImportableNameToken($tokens, $token, $index)) {
            return;
        }

        $name = NameFactory::createFromTokensAndEnd($tokens, $index);
        if ($this->configuration[self::ALLOW_SINGLE_NAMES_OPTION] && $name->isSingleName()) {
            return;
        }

        $name = $this->uniquateLastPart($name);

        // replace with last name part
        $tokens->overrideRange($name->getStart(), $name->getEnd(), [$name->getLastNameToken()]);

        $this->newUseStatementNames[] = $name;
    }

    private function processDocCommentToken(int $index, Tokens $tokens): void
    {
        $docBlockWrapper = $this->docBlockWrapperFactory->create($tokens, $index, $tokens[$index]->getContent());
        // require for doc block changes
        $docBlockWrapper->setWhitespacesFixerConfig($this->whitespacesFixerConfig);

        $oldDocBlockContent = $docBlockWrapper->getContent();

        $this->processParamsTags($docBlockWrapper, $tokens);
        $this->processReturnTag($docBlockWrapper, $tokens);
        $this->processVarTag($docBlockWrapper, $tokens);

        if ($oldDocBlockContent === $docBlockWrapper->getContent()) {
            return;
        }

        // save doc comment
        $tokens[$index] = new Token([T_DOC_COMMENT, $docBlockWrapper->getContent()]);
    }

    private function processReturnTag(DocBlockWrapper $docBlockWrapper, Tokens $tokens): void
    {
        $returnTag = $docBlockWrapper->getReturnTag();
        if (! $returnTag) {
            return;
        }

        $fullName = $this->shortenNameAndReturnFullName($returnTag);
        if (! $fullName) {
            return;
        }

        $this->newUseStatementNames[] = NameFactory::createFromStringAndTokens($fullName, $tokens);
    }

    private function processParamsTags(DocBlockWrapper $docBlockWrapper, Tokens $tokens): void
    {
        foreach ($docBlockWrapper->getParamTags() as $paramTag) {
            $fullName = $this->shortenNameAndReturnFullName($paramTag);
            if (! $fullName) {
                return;
            }

            $this->newUseStatementNames[] = NameFactory::createFromStringAndTokens($fullName, $tokens);
        }
    }

    private function processVarTag(DocBlockWrapper $docBlockWrapper, Tokens $tokens): void
    {
        $returnTag = $docBlockWrapper->getVarTag();
        if (! $returnTag) {
            return;
        }

        $fullName = $this->shortenNameAndReturnFullName($returnTag);
        if (! $fullName) {
            return;
        }

        $this->newUseStatementNames[] = NameFactory::createFromStringAndTokens($fullName, $tokens);
    }

    /**
     * @param Param|TolerantReturn|TolerantParam|Return_|Var_ $tag
     */
    private function shortenNameAndReturnFullName(Tag $tag): ?string
    {
        $objectType = $tag->getType();
        if (! $objectType instanceof Object_) {
            return null;
        }

        $fqsen = $objectType->getFqsen();
        if (! $fqsen instanceof Fqsen) {
            return null;
        }

        $usedName = (string) $fqsen;
        $lastName = $fqsen->getName();

        if ($lastName === ltrim($usedName, '\\')) {
            return null;
        }

        // set new short name
        (new PrivatesSetter())->setPrivateProperty($objectType, 'fqsen', new Fqsen('\\' . $lastName));

        return $usedName;
    }

    private function shouldBeUniquated(Name $newUseStatementName, Name $name): bool
    {
        if ($newUseStatementName->getLastName() !== $name->getLastName()) {
            return false;
        }

        if ($newUseStatementName->getName() === $name->getName()) {
            return false;
        }

        return true;
    }
}
