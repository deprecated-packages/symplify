<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Architecture;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\TokenRunner\Analyzer\SnifferAnalyzer\Naming;
use function Safe\sprintf;

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
        $className = $this->naming->getClassName($file, $position);
        if (! isset($this->oldToPreferredClasses[$className])) {
            return;
        }

        $preferredClass = $this->oldToPreferredClasses[$className];
        $file->addError(
            sprintf('Instead of "%s" class, use "%s"', $className, $preferredClass),
            $position,
            self::class
        );
    }
}
