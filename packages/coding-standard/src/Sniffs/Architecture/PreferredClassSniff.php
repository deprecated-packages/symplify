<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Architecture;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\Rules\PreferredClassRule;
use Symplify\CodingStandard\TokenRunner\Analyzer\SnifferAnalyzer\Naming;

/**
 * @deprecated
 */
final class PreferredClassSniff implements Sniff
{
    /**
     * @var string[]
     */
    public $oldToPreferredClasses = [];

    /**
     * @var Naming
     */
    private $naming;

    public function __construct(Naming $naming)
    {
        $this->naming = $naming;

        trigger_error(sprintf(
            'Sniff "%s" is deprecated. Use "%s" instead',
            self::class,
            PreferredClassRule::class
        ));

        sleep(3);
    }

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_STRING];
    }

    /**
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        if ($this->oldToPreferredClasses === []) {
            return;
        }

        $className = $this->naming->getClassName($file, $position);
        if (! isset($this->oldToPreferredClasses[$className])) {
            return;
        }

        $preferredClass = $this->oldToPreferredClasses[$className];
        $file->addError($this->createMessage($className, $preferredClass), $position, self::class);
    }

    private function createMessage(string $className, string $preferredCase): string
    {
        // class
        if (class_exists($preferredCase)) {
            return sprintf('Instead of "%s" class, use "%s"', $className, $preferredCase);
        }

        // advice
        return sprintf('You should not use "%s". Instead, %s', $className, lcfirst($preferredCase));
    }
}
