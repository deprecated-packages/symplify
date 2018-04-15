<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Import;

use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\DefinedFixerInterface;
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
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Object_;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use SplFileInfo;
use Symplify\BetterReflectionDocBlock\PhpDocParser\PhpDocInfoPrinter;
use Symplify\BetterReflectionDocBlock\Tag\TolerantParam;
use Symplify\BetterReflectionDocBlock\Tag\TolerantReturn;
use Symplify\BetterReflectionDocBlock\Tag\TolerantVar;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;
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
final class ImportNamespacedNameFixer implements DefinedFixerInterface, ConfigurationDefinitionFixerInterface
{
    /**
     * @var string
     */
    private const ALLOW_SINGLE_NAMES_OPTION = 'allow_single_names';

    /**
     * @var string
     */
    private const INCLUDE_DOC_BLOCKS_OPTION = 'include_doc_blocks';

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

    /**
     * @var UseImportsTransformer
     */
    private $useImportsTransformer;

    /**
     * @var ClassNameFinder
     */
    private $classNameFinder;

    /**
     * @var NameAnalyzer
     */
    private $nameAnalyzer;

    /**
     * @var NameFactory
     */
    private $nameFactory;

    /**
     * @var NamespaceUsesAnalyzer
     */
    private $namespaceUsesAnalyzer;
    /**
     * @var PhpDocInfoPrinter
     */
    private $phpDocInfoPrinter;

    public function __construct(
        DocBlockWrapperFactory $docBlockWrapperFactory,
        WhitespacesFixerConfig $whitespacesFixerConfig,
        UseImportsTransformer $useImportsTransformer,
        ClassNameFinder $classNameFinder,
        NameAnalyzer $nameAnalyzer,
        NameFactory $nameFactory,
        NamespaceUsesAnalyzer $namespaceUsesAnalyzer,
        PhpDocInfoPrinter $phpDocInfoPrinter
    ) {
        $this->docBlockWrapperFactory = $docBlockWrapperFactory;
        $this->useImportsTransformer = $useImportsTransformer;
        $this->classNameFinder = $classNameFinder;
        $this->nameAnalyzer = $nameAnalyzer;
        $this->nameFactory = $nameFactory;
        $this->namespaceUsesAnalyzer = $namespaceUsesAnalyzer;

        // set defaults
        $this->configuration = $this->getConfigurationDefinition()
            ->resolve([]);
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
        $this->phpDocInfoPrinter = $phpDocInfoPrinter;
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
        $this->namespaceUseAnalyses = $this->namespaceUsesAnalyzer->getDeclarationsFromTokens($tokens);
        $this->newUseStatementNames = [];

        $currentClassName = $this->classNameFinder->findInTokens($tokens);

        for ($index = $tokens->getSize() - 1; $index > 0; --$index) {
            $token = $tokens[$index];

            // class name is same as token that could be imported, skip
            if ($token->getContent() === $currentClassName) {
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

        if ($this->newUseStatementNames) {
            $this->useImportsTransformer->addNamesToTokens($this->newUseStatementNames, $tokens);
        }
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
            ->setDefault(true)
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
        if (! $this->nameAnalyzer->isImportableNameToken($tokens, $token, $index)) {
            return;
        }

        $name = $this->nameFactory->createFromTokensAndEnd($tokens, $index);
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

        $this->processParamsTags($docBlockWrapper, $tokens);
        $this->processReturnTag($docBlockWrapper, $tokens);
        $this->processVarTag($docBlockWrapper, $tokens);

        $phpDocContent = $this->phpDocInfoPrinter->printFormatPreserving($docBlockWrapper->getPhpDocInfo());

        // save doc comment
        $tokens[$index] = new Token([T_DOC_COMMENT, $phpDocContent]);
    }

    private function processReturnTag(DocBlockWrapper $docBlockWrapper, Tokens $tokens): void
    {
        $returnTagValues = $docBlockWrapper->getPhpDocInfo()->getPhpDocNode()->getReturnTagValues();
        if (! $returnTagValues) {
            return;
        }

        $fullName = $this->shortenNameAndReturnFullNameNew($returnTagValues[0]);
        if (! $fullName) {
            return;
        }

        $this->newUseStatementNames[] = $this->nameFactory->createFromStringAndTokens($fullName, $tokens);
    }

    private function processParamsTags(DocBlockWrapper $docBlockWrapper, Tokens $tokens): void
    {
        $paramTagValues = $docBlockWrapper->getPhpDocInfo()->getPhpDocNode()->getParamTagValues();
        if (! $paramTagValues) {
            return;
        }

        foreach ($paramTagValues as $key => $paramTagValue) {
            $fullName = $this->shortenNameAndReturnFullNameNew($paramTagValue);
            if (! $fullName) {
                return;
            }

            $this->newUseStatementNames[] = $this->nameFactory->createFromStringAndTokens($fullName, $tokens);
        }
    }

    /**
     * @todo same as other two, apart input type; merge 3 to 1
     */
    private function processVarTag(DocBlockWrapper $docBlockWrapper, Tokens $tokens): void
    {
        $varTagValues = $docBlockWrapper->getPhpDocInfo()->getPhpDocNode()->getVarTagValues();
        $this->processPhpDocTagValueNode($varTagValues, $tokens);
    }

    private function processPhpDocTagValueNode(array $tagValues, Tokens $tokens): void
    {
        if (! $tagValues) {
            return;
        }

        $fullName = $this->shortenNameAndReturnFullNameNew($tagValues[0]);
        if (! $fullName) {
            return;
        }

        $this->newUseStatementNames[] = $this->nameFactory->createFromStringAndTokens($fullName, $tokens);
    }

    /**
     * @param ParamTagValueNode|ReturnTagValueNode|VarTagValueNode $phpDocTagValueNode
     */
    private function shortenNameAndReturnFullNameNew(PhpDocTagValueNode $phpDocTagValueNode): ?string
    {
        if (! $phpDocTagValueNode->type instanceof IdentifierTypeNode) {
            return null;
        }

        $usedName = $phpDocTagValueNode->type->name;
        $nameParts = explode('\\', $phpDocTagValueNode->type->name);
        $lastName = array_pop($nameParts);

        if ($lastName === ltrim($phpDocTagValueNode->type->name, '\\')) {
            return null;
        }

        $phpDocTagValueNode->type->name = $lastName;

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
