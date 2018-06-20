<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Naming;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\TokenRunner\Wrapper\SnifferWrapper\ClassWrapperFactory;

final class ClassNameSuffixByParentSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Class "%s" should have suffix "%s" by parent class/interface';

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
        $classWrapper = $this->classWrapperFactory->createFromFirstClassInFile($file);
        $className = $classWrapper->getClassName();
        if ($className === null) {
            return;
        }

        $parentClassName = $classWrapper->getParentClassName();
        if ($parentClassName) {
            $this->processType($file, $parentClassName, $className, $position);
        }

        foreach ($classWrapper->getPartialInterfaceNames() as $interfaceName) {
            $this->processType($file, $interfaceName, $className, $position);
        }
    }

    private function processType(File $file, string $parentType, string $className, int $position): void
    {
        foreach ($this->getClassToSuffixMap() as $classMatch => $suffix) {
            if (! fnmatch($classMatch, $parentType) && ! fnmatch($classMatch . 'Interface', $parentType)) {
                continue;
            }

            if (Strings::endsWith($className, $suffix)) {
                continue;
            }

            $file->addError(sprintf(self::ERROR_MESSAGE, $className, $suffix), $position, self::class);
        }
    }

    /**
     * @return string[]
     */
    private function getClassToSuffixMap(): array
    {
        $classToSuffixMap = array_merge($this->defaultParentClassToSuffixMap, $this->extraParentTypesToSuffixes);

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
