<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Namespaces;

use DateTime;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Sniffs\Namespaces\ReferenceUsedNamesOnlySniff;
use SplFileInfo;
use stdClass;
use Throwable;

/**
 * @deprecated Will be removed in 3.0.
 * Use @see \SlevomatCodingStandard\Sniffs\Namespaces\ReferenceUsedNamesOnlySniff instead.
 */
final class ClassNamesWithoutPreSlashSniff implements Sniff
{
    /**
     * @var string[]
     */
    private $excludedClassNames = [
        DateTime::class, stdClass::class, SplFileInfo::class, Throwable::class,
    ];

    public function __construct()
    {
        trigger_error(sprintf(
            'Class "%s" was deprecated in favor of "%s" that performs the same check. Use it instead.',
            self::class,
            ReferenceUsedNamesOnlySniff::class
        ), E_USER_DEPRECATED);
    }

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_NEW, T_INSTANCEOF];
    }

    /**
     * @param File $file
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $tokens = $file->getTokens();
        $classNameStart = $tokens[$position + 2]['content'];

        if ($classNameStart === '\\') {
            if ($this->isExcludedClassName($tokens[$position + 3]['content'])) {
                return;
            }
            $file->addError(
                'Class name after new/instanceof should not start with slash.',
                $position,
                self::class
            );
        }
    }

    private function isExcludedClassName(string $className): bool
    {
        if (in_array($className, $this->excludedClassNames)) {
            return true;
        }

        return false;
    }
}
