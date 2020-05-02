<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\CleanCode;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\Rules\ForbiddenParentClassRule;
use Symplify\CodingStandard\TokenRunner\Wrapper\SnifferWrapper\SniffClassWrapperFactory;

/**
 * @deprecated
 */
final class ForbiddenParentClassSniff implements Sniff
{
    /**
     * @var string[]
     */
    public $forbiddenParentClasses = [];

    /**
     * @var SniffClassWrapperFactory
     */
    private $sniffClassWrapperFactory;

    public function __construct(SniffClassWrapperFactory $sniffClassWrapperFactory)
    {
        $this->sniffClassWrapperFactory = $sniffClassWrapperFactory;

        trigger_error(sprintf(
            'Sniff "%s" is deprecated. Use "%s" instead',
            self::class,
            ForbiddenParentClassRule::class
        ));

        sleep(3);
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
        if ($this->forbiddenParentClasses === []) {
            return;
        }

        $classWrapper = $this->sniffClassWrapperFactory->createFromFirstClassInFile($file);

        // no class
        if ($classWrapper === null) {
            return;
        }

        // anonymous class only
        $className = $classWrapper->getClassName();
        if ($className === null) {
            return;
        }

        $parentClassName = $classWrapper->getParentClassName();
        // no parent class
        if ($parentClassName === null) {
            return;
        }

        if ($this->shouldSkip($parentClassName)) {
            return;
        }

        $file->addError(
            sprintf('Class "%s" cannot be parent class. Use composition over inheritance instead.', $parentClassName),
            $position,
            self::class
        );
    }

    private function shouldSkip(string $parentClassName): bool
    {
        foreach ($this->forbiddenParentClasses as $forbiddenParentClass) {
            if ($parentClassName === $forbiddenParentClass) {
                return false;
            }

            if (fnmatch($forbiddenParentClass, $parentClassName)) {
                return false;
            }
        }

        return true;
    }
}
