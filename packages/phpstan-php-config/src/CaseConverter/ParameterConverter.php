<?php

declare(strict_types=1);

namespace Symplify\PHPStanPHPConfig\CaseConverter;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symplify\PHPStanPHPConfig\ValueObject\Option;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ParameterConverter
{
    /**
     * @return mixed[]
     */
    public function convertParameterBag(ParameterBagInterface $parameterBag): array
    {
        $neonParameters = [];

        if ($parameterBag->has(Option::LEVEL)) {
            $neonParameters[Option::LEVEL] = $parameterBag->get(Option::LEVEL);
        }

        if ($parameterBag->has(Option::PATHS)) {
            $neonParameters[Option::PATHS] = $this->resolvePaths($parameterBag);
        }

        if ($parameterBag->has(Option::REPORT_UNMATCHED_IGNORED_ERRORS)) {
            // to prevent full thread lagging pc
            $neonParameters[Option::REPORT_UNMATCHED_IGNORED_ERRORS] = (bool) $parameterBag->get(
                Option::REPORT_UNMATCHED_IGNORED_ERRORS
            );
        }

        if ($parameterBag->has(Option::PARALLEL_MAX_PROCESSES)) {
            // to prevent full thread lagging pc
            $neonParameters['parallel'][Option::PARALLEL_MAX_PROCESSES] = (int) $parameterBag->get(
                Option::PARALLEL_MAX_PROCESSES
            );
        }

        return $neonParameters;
    }

    /**
     * @return string[]
     */
    private function resolvePaths(ParameterBagInterface $parameterBag): array
    {
        // relativize paths to root
        $paths = (array) $parameterBag->get(Option::PATHS);

        $relativePaths = [];
        foreach ($paths as $path) {
            $pathFileInfo = new SmartFileInfo($path);
            $relativePaths[] = $pathFileInfo->getRelativeFilePathFromCwdInTests();
        }

        return $relativePaths;
    }
}
