<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\Console\Output;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\TemplateChecker\ValueObject\ClassMethodName;

final class StaticClassMethodNamesReporter
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }

    /**
     * @param ClassMethodName[] $classMethodNames
     */
    public function reportClassMethodNames(array $classMethodNames): void
    {
        foreach ($classMethodNames as $classMethodName) {
            $classMethodMessage = sprintf('Static call "%s()" found', $classMethodName->getClassMethodName());
            $this->symfonyStyle->title($classMethodMessage);
            $this->symfonyStyle->writeln('Template call located at: ' . $classMethodName->getLatteFilePath());

            if (! $classMethodName->isOnVariableStaticCall()) {
                $this->symfonyStyle->writeln('Method located at: ' . $classMethodName->getFileLine());
            }

            $this->symfonyStyle->newLine(2);
        }
    }
}
