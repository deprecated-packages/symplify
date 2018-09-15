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
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use SplFileInfo;
use Symplify\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Symplify\BetterPhpDocParser\Printer\PhpDocInfoPrinter;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\ClassNameFinder;
use Symplify\TokenRunner\Naming\Name\Name;
use Symplify\TokenRunner\Naming\Name\NameAnalyzer;
use Symplify\TokenRunner\Naming\Name\NameFactory;
use Symplify\TokenRunner\Transformer\FixerTransformer\UseImportsTransformer;
use function Safe\sleep;
use function Safe\sprintf;

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
     * @var Name[]
     */
    private $newUseStatementNames = [];

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

    /**
     * @var PhpDocInfoFactory
     */
    private $phpDocInfoFactory;

    public function __construct(
        UseImportsTransformer $useImportsTransformer,
        ClassNameFinder $classNameFinder,
        NameAnalyzer $nameAnalyzer,
        NameFactory $nameFactory,
        NamespaceUsesAnalyzer $namespaceUsesAnalyzer,
        PhpDocInfoPrinter $phpDocInfoPrinter,
        PhpDocInfoFactory $phpDocInfoFactory
    ) {
        $this->useImportsTransformer = $useImportsTransformer;
        $this->classNameFinder = $classNameFinder;
        $this->nameAnalyzer = $nameAnalyzer;
        $this->nameFactory = $nameFactory;
        $this->namespaceUsesAnalyzer = $namespaceUsesAnalyzer;

        // set defaults
        $this->configuration = $this->getConfigurationDefinition()
            ->resolve([]);
        $this->phpDocInfoPrinter = $phpDocInfoPrinter;
        $this->phpDocInfoFactory = $phpDocInfoFactory;

        trigger_error(
            sprintf(
                '"%s" was deprecated and will be removed in Symplify\CodingStandard 5.0. Use "%s" instead."',
                self::class,
                'SlevomatCodingStandard\Sniffs\Namespaces\ReferenceUsedNamesOnlySniff'
            ),
            E_USER_DEPRECATED
        );
        sleep(3); // inspired at "deprecated interface" Tweet
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Types should not be referenced via a fully/partially qualified name, but via a use statement.',
            [new CodeSample('<?php $value = \SomeNamespace\SomeClass')]
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

    public function getName(): string
    {
        return self::class;
    }

    /**
     * There are still some edge cases to be found and improve.
     */
    public function isRisky(): bool
    {
        return true;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
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
        if (! $this->configuration[self::INCLUDE_DOC_BLOCKS_OPTION]) {
            return;
        }

        $phpDocInfo = $this->phpDocInfoFactory->createFrom($tokens[$index]->getContent());
        $phpDocNode = $phpDocInfo->getPhpDocNode();

        // require for doc block changes
        $this->processPhpDocTagValueNode($phpDocNode->getReturnTagValues(), $tokens);
        $this->processPhpDocTagValueNode($phpDocNode->getParamTagValues(), $tokens);
        $this->processPhpDocTagValueNode($phpDocNode->getVarTagValues(), $tokens);

        $phpDocContent = $this->phpDocInfoPrinter->printFormatPreserving($phpDocInfo);

        // save doc comment
        $tokens[$index] = new Token([T_DOC_COMMENT, $phpDocContent]);
    }

    /**
     * @param ParamTagValueNode[]|ReturnTagValueNode[]|VarTagValueNode[] $tagValues
     */
    private function processPhpDocTagValueNode(array $tagValues, Tokens $tokens): void
    {
        foreach ($tagValues as $tagValue) {
            $this->traverseTypeNode($tagValue->type, $tokens);
        }
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

    private function traverseTypeNode(TypeNode $typeNode, Tokens $tokens): TypeNode
    {
        if ($typeNode instanceof ArrayTypeNode) {
            $typeNode->type = $this->traverseTypeNode($typeNode->type, $tokens);
        }

        if ($typeNode instanceof UnionTypeNode) {
            foreach ($typeNode->types as $key => $subTypeNode) {
                $typeNode->types[$key] = $this->traverseTypeNode($subTypeNode, $tokens);
            }
        }

        if ($typeNode instanceof IdentifierTypeNode) {
            $this->processIdentifierTypeNode($typeNode, $tokens);
        }

        return $typeNode;
    }

    private function processIdentifierTypeNode(IdentifierTypeNode $identifierTypeNode, Tokens $tokens): void
    {
        $usedName = $identifierTypeNode->name;
        $nameParts = explode('\\', $identifierTypeNode->name);
        $lastName = array_pop($nameParts);

        if ($lastName === ltrim($identifierTypeNode->name, '\\')) {
            return;
        }

        $name = $this->nameFactory->createFromStringAndTokens($usedName, $tokens);
        $this->uniquateLastPart($name);

        $identifierTypeNode->name = $name->getLastName();

        $this->newUseStatementNames[] = $name;
    }
}
