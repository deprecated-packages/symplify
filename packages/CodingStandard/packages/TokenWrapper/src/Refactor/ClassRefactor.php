<?php declare(strict_types=1);

namespace Symplify\CodingStandard\TokenWrapper\Refactor;

use Nette\PhpGenerator\Method;
use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Fixer;
use Symplify\CodingStandard\Helper\TokenFinder;
use Symplify\CodingStandard\TokenWrapper\ClassWrapper;

final class ClassRefactor
{
    /**
     * @var File
     */
    private $file;

    /**
     * @var ClassWrapper
     */
    private $classWrapper;

    /**
     * @var Fixer
     */
    private $fixer;

    public function __construct(File $file, ClassWrapper $classWrapper)
    {
        $this->file = $file;
        $this->fixer = $file->fixer;
        $this->classWrapper = $classWrapper;
    }

    public function addConstructorMethodWithProperty(string $propertyType, string $propertyName): void
    {
        $method = $this->createConstructMethod();
        $parameter = $method->addParameter($propertyName);
        $parameter->setTypeHint($propertyType);
        $method->setBody('$this->? = $?;', [$propertyName, $propertyName]);

        $methodCode = Strings::indent((string) $method, 1, '    ');

        $constructorPosition = $this->getConstructorPosition();
        $this->fixer->addContentBefore($constructorPosition, PHP_EOL . $methodCode . PHP_EOL);
    }

    private function createConstructMethod(): Method
    {
        $method = new Method('__construct');
        $method->setVisibility('public');

        return $method;
    }

    private function getConstructorPosition(): int
    {
        $lastPropertyPosition = null;
        foreach ($this->classWrapper->getProperties() as $property) {
            $lastPropertyPosition = $property->getPosition();
        }

        if ($lastPropertyPosition) {
            return TokenFinder::findNextLinePosition($this->file, $lastPropertyPosition);
        }

        // @todo: class opener
    }
}
