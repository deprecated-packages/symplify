<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Naming;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * @deprecated
 */
final class TraitNameSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Trait should have suffix "Trait".';

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
            'Sniff "%s" is deprecated. Use "%s" instead',
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
        return [T_TRAIT];
    }

    /**
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $this->file = $file;
        $this->position = $position;

        if (Strings::endsWith($this->getTraitName(), 'Trait')) {
            return;
        }

        $file->addError(self::ERROR_MESSAGE, $position, self::class);
    }

    private function getTraitName(): string
    {
        return (string) $this->file->getDeclarationName($this->position);
    }
}
