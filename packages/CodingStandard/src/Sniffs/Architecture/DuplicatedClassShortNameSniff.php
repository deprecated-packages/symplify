<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Architecture;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\NamespaceHelper;

final class DuplicatedClassShortNameSniff implements Sniff
{
    /**
     * @var string[]
     */
    public $allowedClassNames = [];

    /**
     * @var string[][]
     */
    private $usedClassNames = [];

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
        $classTokenPosition = $file->findPrevious(T_CLASS, $position, max(1, $position - 3));

        // not a class name
        if ($classTokenPosition === false) {
            return;
        }

        $className = $file->getTokens()[$position]['content'];

        // is allowed
        foreach ($this->allowedClassNames as $allowedClassName) {
            if (fnmatch($allowedClassName, $className, FNM_NOESCAPE)) {
                return;
            }
        }

        $namespace = NamespaceHelper::findCurrentNamespaceName($file, $position);
        if ($namespace) {
            $fullyQualifiedClassName = $namespace . '\\' . $className;
        } else {
            $fullyQualifiedClassName = $className;
        }

        $this->usedClassNames[$className][] = $fullyQualifiedClassName;

        if (count($this->usedClassNames[$className]) <= 1) {
            return;
        }

        $message = sprintf(
            'Class with base "%s" name is already used in "%s". Use specific name to make class unique and easy to recognize from the other.',
            $className,
            implode('", "', $this->usedClassNames[$className])
        );

        $file->addError($message, $position, self::class);
    }
}
