<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Latte;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCI\PhpParser\LatteFilterProviderFactory;
use Symplify\EasyCI\ValueObject\ClassMethodName;
use Symplify\SmartFileSystem\SmartFileSystem;

final class LatteFilterProviderFileGenerator
{
    /**
     * @var LatteFilterProviderFactory
     */
    private $latteFilterProviderFactory;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(
        LatteFilterProviderFactory $latteFilterProviderFactory,
        SmartFileSystem $smartFileSystem,
        SymfonyStyle $symfonyStyle
    ) {
        $this->latteFilterProviderFactory = $latteFilterProviderFactory;
        $this->smartFileSystem = $smartFileSystem;
        $this->symfonyStyle = $symfonyStyle;
    }

    public function generate(ClassMethodName $classMethodName): void
    {
        $generatedContent = $this->latteFilterProviderFactory->createFromClassMethodName($classMethodName);

        $filterProviderClassName = $classMethodName->getFilterProviderClassName();
        $shortFilePath = 'generated/' . $filterProviderClassName . '.php';

        $this->smartFileSystem->dumpFile(getcwd() . '/' . $shortFilePath, $generatedContent);

        $generateMessage = sprintf('File "%s" was generated', $shortFilePath);
        $this->symfonyStyle->note($generateMessage);
    }
}
