<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\Helper\Naming;
use Symplify\CodingStandard\TokenWrapper\ClassWrapper;

final class EqualInterfaceImplementationSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Implementation of interface should only contain its methods.'
        . ' Extra public methods found: %s.';

    /**
     * @var string[]
     */
    public $interfacesToSkip = [
        'Symfony\Component\EventDispatcher\EventSubscriberInterface',
    ];

    /**
     * @var ClassWrapper
     */
    private $classWrapper;

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
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $this->file = $file;
        $this->position = $position;
        $this->classWrapper = ClassWrapper::createFromFileAndPosition($file, $position);

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
        if (! $this->implementsInterface()) {
            return true;
        }

        if (Naming::isControllerClass($this->file, $this->position)) {
            return true;
        }

        $inheritsSkippedInterface = (bool) array_intersect(
            $this->classWrapper->getInterfaces(),
            $this->interfacesToSkip
        );

        if ($inheritsSkippedInterface) {
            return true;
        }

        return false;
    }

    private function implementsInterface(): bool
    {
        return (bool) $this->file->findNext(T_IMPLEMENTS, $this->position, $this->position + 5);
    }

    /**
     * @return string[]
     */
    private function getExtraPublicMethodNames(): array
    {
        $publicMethodNames = array_keys($this->classWrapper->getPublicMethods());
        $extraPublicMethodNames = array_diff($publicMethodNames, $this->classWrapper->getInterfacesRequiredMethods());

        return $extraPublicMethodNames;
    }
}
