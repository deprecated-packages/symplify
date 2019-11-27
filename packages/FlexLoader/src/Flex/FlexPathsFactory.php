<?php declare(strict_types=1);

namespace Symplify\FlexLoader\Flex;

use Nette\Utils\Strings;

final class FlexPathsFactory
{
    /**
     * @param string[] $extraServicePaths
     * @return string[]
     */
    public function createServicePaths(string $projectDir, string $environment, array $extraServicePaths): array
    {
        $servicePaths = [
            $projectDir . '/config/packages/*',
            $projectDir . '/config/packages/' . $environment . '/*',
            $projectDir . '/config/services',
            $projectDir . '/config/services_' . $environment,
            $projectDir . '/config/parameters',
            $projectDir . '/config/parameters_' . $environment,
        ];

        return $this->filterExistingPaths(array_merge($servicePaths, $extraServicePaths));
    }

    /**
     * @return string[]
     */
    public function createRoutingPaths(string $projectDir, string $environment): array
    {
        $routingPaths = [
            $projectDir . '/config/routes/*',
            $projectDir . '/config/routes/' . $environment . '/*',
            $projectDir . '/config/routes',
        ];

        return $this->filterExistingPaths($routingPaths);
    }

    /**
     * This is needed, because $config->load() throws exception on non-existing glob paths
     * @param string[] $globPaths
     * @return string[]
     */
    private function filterExistingPaths(array $globPaths): array
    {
        $existingGlobPaths = [];
        foreach ($globPaths as $globPath) {
            $checkedGlobPath = ! Strings::endsWith($globPath, '*') ? $globPath . '*' : $globPath;

            if (glob($checkedGlobPath)) {
                $existingGlobPaths[] = $globPath;
            }
        }

        return $existingGlobPaths;
    }
}
