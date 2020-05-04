<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Naming;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * @deprecated
 */
final class AbstractClassNameSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Abstract class should have prefix "Abstract".';

    /**
     * @var int
     */
    private $position;

    /**
     * @var File
     */
    private $file;

    public function __construct()
    {
        trigger_error(sprintf(
            'Sniff "%s" is deprecated and will be removed in Symplify 8 (May 2020). Use "%s" instead',
            self::class,
            'https://github.com/Slamdunk/phpstan-extensions'
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
        $this->file = $file;
        $this->position = $position;

        if ($this->shouldBeSkipped()) {
            return;
        }

        $file->addError(self::ERROR_MESSAGE, $position, self::class);
    }

    private function shouldBeSkipped(): bool
    {
        if (! $this->isClassAbstract()) {
            return true;
        }

        $className = $this->getClassName();
        // anonymous
        if ($className === null) {
            return true;
        }

        return Strings::startsWith($className, 'Abstract');
    }

    private function isClassAbstract(): bool
    {
        $classProperties = $this->file->getClassProperties($this->position);

        return $classProperties['is_abstract'];
    }

    private function getClassName(): ?string
    {
        return $this->file->getDeclarationName($this->position);
    }
}
