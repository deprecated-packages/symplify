<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\DependencyInjection;

use DateTime;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;

final class NoClassInstantiationSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Use service and constructor injection rather than manual new %s.';

    /**
     * @var string[]
     */
    public $allowedClasses = [
        DateTime::class,
    ];

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_NEW];
    }

    public function process(File $file, $position): void
    {
        $classNameTokenPosition = TokenHelper::findNext($file, [T_STRING], $position);
        if ($classNameTokenPosition === null) {
            return;
        }

        $tokens = $file->getTokens();
        $classNameToken = $tokens[$classNameTokenPosition];
        $className = $classNameToken['content'];

        if ($this->isClassInstantiationAllowed($className)) {
            return;
        }

        $file->addError(sprintf(
            self::ERROR_MESSAGE,
            $className
        ), $position, self::class);
    }

    private function isClassInstantiationAllowed(string $class): bool
    {
        return in_array($class, $this->allowedClasses, true);
    }
}
