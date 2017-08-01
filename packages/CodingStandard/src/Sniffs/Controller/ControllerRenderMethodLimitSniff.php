<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Controller;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\Helper\Naming;
use Symplify\CodingStandard\TokenWrapper\ClassWrapper;
use Symplify\CodingStandard\TokenWrapper\MethodWrapper;

final class ControllerRenderMethodLimitSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Controller has %d render methods. Max limit is %d.';

    /**
     * @var int
     */
    public $maxCount = 1;

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
    public function register()
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

        $renderMethodCount = $this->getRenderMethodCount($file, $position);
        if ($renderMethodCount <= $this->maxCount) {
            return;
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $renderMethodCount, $this->maxCount);

        $file->addError($errorMessage, $position, self::class);
    }

    private function shouldBeSkipped(): bool
    {
        if (Naming::isControllerClass($this->file, $this->position)) {
            return false;
        }

        return true;
    }

    private function getRenderMethodCount(File $file, $position): int
    {
        $classWrapper = ClassWrapper::createFromFileAndPosition($file, $position);

        $renderMethodCount = 0;
        foreach ($classWrapper->getMethods() as $method) {
            if ($this->isControllerRenderMethod($method)) {
                ++$renderMethodCount;
            }
        }

        return $renderMethodCount;
    }

    private function isControllerRenderMethod(MethodWrapper $method): bool
    {
        if (! $method->isPublic()) {
            return false;
        }

        if (Strings::contains($method->getName(), 'render')) {
            return true;
        }

        if (Strings::contains($method->getName(), 'Render')) {
            return true;
        }

        return false;
    }
}
