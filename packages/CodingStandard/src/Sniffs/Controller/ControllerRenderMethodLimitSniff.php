<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Controller;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\Helper\Naming;
use Symplify\CodingStandard\TokenWrapper\ClassWrapper;

final class ControllerRenderMethodLimitSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Controller can have up to %d render methods. %d found.';

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
     * @param File $file
     * @param int $position
     */
    public function process(File $file, $position)
    {
        $this->file = $file;
        $this->position = $position;

        if ($this->shouldBeSkipped()) {
            return;
        }

        $classWrapper = ClassWrapper::createFromFileAndPosition($file, $position);
        $renderMethodCount = 0;
        foreach ($classWrapper->getMethods() as $method) {
            if (! $method->isPublic()) {
                continue;
            }

            if (Strings::contains($method->getName(), 'render')) {
                ++$renderMethodCount;
            }

            if (Strings::contains($method->getName(), 'Render')) {
                ++$renderMethodCount;
            }

            if (Strings::contains($method->getName(), 'action')) {
                ++$renderMethodCount;
            }
        }

        if ($renderMethodCount <= $this->maxCount) {
            return;
        }

        $errorMessage = sprintf(
            self::ERROR_MESSAGE,
            $renderMethodCount,
            $this->maxCount
        );

        $file->addError($errorMessage, $position, self::class);
    }

    private function shouldBeSkipped(): bool
    {
        if (Naming::isControllerClass($this->file, $this->position)) {
            return false;
        }

        return true;
    }
}
