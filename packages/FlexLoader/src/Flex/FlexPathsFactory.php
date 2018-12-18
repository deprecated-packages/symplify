<?php declare(strict_types=1);

namespace Symplify\FlexLoader\Flex;

use Nette\Utils\Strings;
use function Safe\glob;

final class FlexPathsFactory
{
    /**
     * @return string[]
     */
    public function createServicePaths(string $projectDir, string $environment): array
    {
        $servicePaths = [
            $projectDir . '/config/packages/*',
            $projectDir . '/config/packages/' . $environment . '/**/*',
            $projectDir . '/config/services',
            $projectDir . '/config/services_' . $environment,
        ];

        return $this->filterExistingPaths($servicePaths);
    }

    /**
     * @return string[]
     */
    public function createRoutingPaths(string $projectDir, string $environment): array
    {
        $routingPaths = [
            $projectDir . '/config/routes/*',
            $projectDir . '/config/routes/' . $environment . '/**/*',
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
            // the final glob is decorated with *, so we need to check it here too
            if (! Strings::endsWith($globPath, '*')) {
                $checkedGlobPath = $globPath . '*';
            } else {
                $checkedGlobPath = $globPath;
            }

            if (glob($checkedGlobPath)) {
                $existingGlobPaths[] = $globPath;
            }
        }

        return $existingGlobPaths;
    }
}
