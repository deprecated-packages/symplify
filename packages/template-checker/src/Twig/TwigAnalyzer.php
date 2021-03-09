<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\Twig;

use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\TemplateChecker\Template\RenderMethodTemplateExtractor;

final class TwigAnalyzer
{
    /**
     * @var RenderMethodTemplateExtractor
     */
    private $renderMethodTemplateExtractor;

    public function __construct(RenderMethodTemplateExtractor $renderMethodTemplateExtractor)
    {
        $this->renderMethodTemplateExtractor = $renderMethodTemplateExtractor;
    }

    /**
     * @param SmartFileInfo[] $controllerFileInfos
     * @param string[] $allowedTemplatePaths
     * @return string[]
     */
    public function analyzeFileInfos(array $controllerFileInfos, array $allowedTemplatePaths): array
    {
        $usedTemplatePaths = $this->renderMethodTemplateExtractor->extractFromFileInfos($controllerFileInfos);

        $errorMessages = [];

        foreach ($usedTemplatePaths as $relativeControllerFilePath => $usedTemplatePaths) {
            foreach ($usedTemplatePaths as $usedTemplatePath) {
                if (in_array($usedTemplatePath, $allowedTemplatePaths, true)) {
                    continue;
                }

                $errorMessages[] = sprintf(
                    'Template reference "%s" used in "%s" controller was not found in existing templates',
                    $usedTemplatePath,
                    $relativeControllerFilePath
                );
            }
        }

        return $errorMessages;
    }
}
