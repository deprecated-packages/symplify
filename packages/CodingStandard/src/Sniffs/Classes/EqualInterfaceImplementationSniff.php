<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\TokenWrapper\ClassWrapper;

final class EqualInterfaceImplementationSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Implementation of interface should only contain its methods.'
        . ' Extra public methods found: %s.';

    /**
     * @var File
     */
    private $file;

    /**
     * @var int
     */
    private $position;

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_CLASS];
    }

    /**
     * @param File $file
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $this->file = $file;
        $this->position = $position;

        if ($this->shouldBeSkipped()) {
            return;
        }

        $extraPublicMethodNames = $this->getExtraPublicMethodNames();
        if (count($extraPublicMethodNames) === 0) {
            return;
        }

        $errorMessage = sprintf(
            self::ERROR_MESSAGE,
            implode($extraPublicMethodNames, ', ')
        );

        $file->addError($errorMessage, $position, self::class);
    }

    private function shouldBeSkipped(): bool
    {
        return ! $this->implementsInterface();
    }

    private function implementsInterface(): bool
    {
        return (bool) $this->file->findNext(T_IMPLEMENTS, $this->position);
    }

    /**
     * @return string[]
     */
    private function getExtraPublicMethodNames(): array
    {
        $classWrapper = ClassWrapper::createFromFileAndPosition($this->file, $this->position);
        $publicMethodNames = array_keys($classWrapper->getPublicMethods());
        $extraPublicMethodNames = array_diff($publicMethodNames, $classWrapper->getInterfacesRequiredMethods());

        return $extraPublicMethodNames;
    }
}
