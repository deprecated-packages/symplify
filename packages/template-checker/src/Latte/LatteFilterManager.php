<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\Latte;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;
use Symplify\TemplateChecker\StaticCallWithFilterReplacer;
use Symplify\TemplateChecker\ValueObject\ClassMethodName;

final class LatteFilterManager
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var LatteFilterProviderFileGenerator
     */
    private $latteFilterProviderFileGenerator;

    /**
     * @var StaticCallWithFilterReplacer
     */
    private $staticCallWithFilterReplacer;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        LatteFilterProviderFileGenerator $latteFilterProviderFileGenerator,
        StaticCallWithFilterReplacer $staticCallWithFilterReplacer,
        SmartFileSystem $smartFileSystem
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->latteFilterProviderFileGenerator = $latteFilterProviderFileGenerator;
        $this->staticCallWithFilterReplacer = $staticCallWithFilterReplacer;
        $this->smartFileSystem = $smartFileSystem;
    }

    /**
     * @param SmartFileInfo[] $latteFileInfos
     * @param ClassMethodName[] $classMethodNames
     */
    public function manageClassMethodNames(array $latteFileInfos, array $classMethodNames, bool $isFix): void
    {
        $uniqueMethodNames = $this->filterUniqueClassMethodNames($classMethodNames);

        if (! $isFix) {
            $this->symfonyStyle->error(
                'We found some static calls in your templates. Do you want to extract them to latte filter provider? Just re-run command with `--fix` option'
            );
        }

        if ($isFix) {
            $this->generateFilterProviderClasses($uniqueMethodNames);
            $this->updatePathsInTemplates($latteFileInfos);
        }
    }

    private function reportOnVariableStaticCall(ClassMethodName $classMethodName): void
    {
        $message = sprintf(
            'Method "%s()" has unknown class, so it cannot be generated. Handle this case manually by replacing variable by the known class first, then re-running this command.',
            $classMethodName->getClassMethodName()
        );

        $this->symfonyStyle->warning($message);
    }

    /**
     * @param ClassMethodName[] $classMethodNames
     * @return ClassMethodName[]
     */
    private function filterUniqueClassMethodNames(array $classMethodNames): array
    {
        $uniqueClassMethodNames = [];
        foreach ($classMethodNames as $classMethodName) {
            $uniqueClassMethodNames[$classMethodName->getClassMethodName()] = $classMethodName;
        }

        return $uniqueClassMethodNames;
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     */
    private function updatePathsInTemplates(array $fileInfos): void
    {
        foreach ($fileInfos as $fileInfo) {
            $changedContent = $this->staticCallWithFilterReplacer->processFileInfo($fileInfo);
            $this->smartFileSystem->dumpFile($fileInfo->getPathname(), $changedContent);
        }
    }

    /**
     * @param ClassMethodName[] $uniqueClassMethodNames
     */
    private function generateFilterProviderClasses(array $uniqueClassMethodNames): void
    {
        foreach ($uniqueClassMethodNames as $classMethodName) {
            if ($classMethodName->isOnVariableStaticCall()) {
                $this->reportOnVariableStaticCall($classMethodName);
                continue;
            }

            $this->latteFilterProviderFileGenerator->generate($classMethodName);
        }
    }
}
