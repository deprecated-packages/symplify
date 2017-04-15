<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Controller;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\Helper\Naming;
use Symplify\CodingStandard\TokenWrapper\ClassWrapper;

final class InvokableControllerSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Controller has to contain __invoke() method.';

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
    public function process(File $file, $position)
    {
        $this->file = $file;
        $this->position = $position;

        if ($this->shouldBeSkipped()) {
            return;
        }

        $classWrapper = ClassWrapper::createFromFileAndPosition($file, $position);
        foreach ($classWrapper->getMethods() as $method) {
            if ($method->getName() === '__invoke') {
                return;
            }
        }

        $file->addError(self::ERROR_MESSAGE, $position, self::class);
    }

    private function shouldBeSkipped(): bool
    {
        if (Naming::isControllerClass($this->file, $this->position)) {
            return false;
        }

        return true;
    }
}
