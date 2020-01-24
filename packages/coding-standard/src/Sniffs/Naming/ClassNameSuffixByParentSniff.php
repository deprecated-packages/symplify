<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Naming;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\TokenRunner\Wrapper\SnifferWrapper\SniffClassWrapperFactory;

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
        'EventSubscriberInterface',
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
     * @var SniffClassWrapperFactory
     */
    private $sniffClassWrapperFactory;

    public function __construct(SniffClassWrapperFactory $sniffClassWrapperFactory)
    {
        $this->sniffClassWrapperFactory = $sniffClassWrapperFactory;
    }

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_CLASS];
    }

    /**
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $classWrapper = $this->sniffClassWrapperFactory->createFromFirstClassInFile($file);
        if ($classWrapper === null) {
            return;
        }

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

    private function processType(File $file, string $currentParentType, string $className, int $position): void
    {
        foreach ($this->getClassToSuffixMap() as $parentType) {
            if (! fnmatch('*' . $parentType, $currentParentType)) {
                continue;
            }

            // the class that implements $currentParentType, should end with $suffix
            $suffix = $this->resolveExpectedSuffix($parentType);
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
        return array_merge($this->defaultParentClassToSuffixMap, $this->extraParentTypesToSuffixes);
    }

    /**
     * - SomeInterface => Some
     * - SomeAbstract => Some
     * - AbstractSome => Some
     */
    private function resolveExpectedSuffix(string $parentType): string
    {
        if (Strings::endsWith($parentType, 'Interface')) {
            $parentType = Strings::substring($parentType, 0, -strlen('Interface'));
        }

        if (Strings::endsWith($parentType, 'Abstract')) {
            $parentType = Strings::substring($parentType, 0, -strlen('Abstract'));
        }

        if (Strings::startsWith($parentType, 'Abstract')) {
            $parentType = Strings::substring($parentType, strlen('Abstract'));
        }

        return $parentType;
    }
}
