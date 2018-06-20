<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Naming;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ClassWrapper;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ClassWrapperFactory;

final class ClassNameSuffixByParentSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Class should have suffix by parent class/interface';

    /**
     * @var string[]
     */
    public $defaultParentClassToSuffixMap = [
        'Command',
        'Controller',
        'Repository',
        'Presenter',
        'Request',
        'Response',
        'EventSubscriber',
        'FixerInterface',
        'Sniff',
        'Exception',
        'Handler',
    ];

    /**
     * @var string[]
     */
    public $extraParentTypesToSuffixes = [];

    /**
     * @var ClassWrapperFactory
     */
    private $classWrapperFactory;

    public function __construct(ClassWrapperFactory $classWrapperFactory)
    {
        $this->classWrapperFactory = $classWrapperFactory;
    }

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_CLASS, T_INTERFACE];
    }

    /**
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $this->file = $file;
        $this->position = $position;

        // @todo
        $classWrapper = $this->classWrapperFactory->createFromTokensArrayStartPosition($tokens, $position);
        $this->processClassWrapper($tokens, $classWrapper);
    }

    private function processClassWrapper(Tokens $tokens, ClassWrapper $classWrapper): void
    {
        $className = $classWrapper->getName();
        if ($className === null) {
            return;
        }

        $parentClassName = $classWrapper->getParentClassName();
        if ($parentClassName) {
            $this->processType($tokens, $classWrapper, $parentClassName, $className);
        }

        foreach ($classWrapper->getInterfaceNames() as $interfaceName) {
            $this->processType($tokens, $classWrapper, $interfaceName, $className);
        }
    }

    private function processType(
        Tokens $tokens,
        ClassWrapper $classWrapper,
        string $parentType,
        string $className
    ): void {
        $classToSuffixMap = $this->getClassToSuffixMap();

        foreach ($classToSuffixMap as $classMatch => $suffix) {
            if (! fnmatch($classMatch, $parentType) && ! fnmatch($classMatch . 'Interface', $parentType)) {
                continue;
            }

            if (Strings::endsWith($className, $suffix)) {
                continue;
            }

            // report error!
            $file->addError(self::ERROR_MESSAGE, $classWrapper->getNamePosition(, self::class);

//            $tokens[$classWrapper->getNamePosition()] = new Token([T_STRING, $className . $suffix]);
        }
    }

    /**
     * @return string[]
     */
    private function getClassToSuffixMap(): array
    {
        $classToSuffixMap = array_merge($this->defaultParentClassToSuffixMap, $this->extraParentTypesToSuffixes) ;

        $classToSuffixMap = $this->convertListValuesToKeysToSuffixes($classToSuffixMap);

        return $this->prefixKeysWithAsteriks($classToSuffixMap);
    }

    /**
     * From:
     * - [0 => "*Type"]
     * to:
     * - ["*Type" => "Type"]
     *
     * @param string[] $values
     * @return string[]
     */
    private function convertListValuesToKeysToSuffixes(array $values): array
    {
        foreach ($values as $key => $value) {
            if (! is_numeric($key)) {
                continue;
            }

            $suffix = ltrim($value, '*');

            // remove "Interface" suffix
            if (Strings::endsWith($suffix, 'Interface')) {
                $suffix = substr($suffix, 0, -strlen('Interface'));
            }

            $values[$value] = $suffix;

            unset($values[$key]);
        }
        return $values;
    }

    /**
     * From:
     * - ["Type" => "Suffix"]
     * to:
     * - ["*Type" => "Suffix"]
     *
     * @param string[] $values
     * @return string[]
     */
    private function prefixKeysWithAsteriks(array $values): array
    {
        foreach ($values as $key => $value) {
            if (Strings::startsWith($key, '*')) {
                continue;
            }

            $newKey = '*' . $key;

            $values[$newKey] = $value;

            unset($values[$key]);
        }

        return $values;
    }
}
