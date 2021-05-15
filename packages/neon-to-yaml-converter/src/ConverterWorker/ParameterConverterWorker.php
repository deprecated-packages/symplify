<?php

declare(strict_types=1);

namespace Symplify\NeonToYamlConverter\ConverterWorker;

use Symplify\NeonToYamlConverter\ArrayParameterCollector;

/**
 * @see \Symplify\NeonToYamlConverter\Tests\NeonToYamlConverterTest
 */
final class ParameterConverterWorker
{
    /**
     * @var ArrayParameterCollector
     */
    private $arrayParameterCollector;

    public function __construct(ArrayParameterCollector $arrayParameterCollector)
    {
        $this->arrayParameterCollector = $arrayParameterCollector;
    }

    /**
     * @param mixed[] $parametersData
     * @return mixed[]
     */
    public function convert(array $parametersData): array
    {
        foreach ($parametersData as $key => $value) {
            if (! is_array($value)) {
                continue;
            }

            foreach ($value as $key2 => $value2) {
                $oldKey = $key . '.' . $key2;

                $newKey = $this->arrayParameterCollector->matchParameterToReplace($oldKey);
                if ($newKey === null) {
                    continue;
                }

                // replace key
                unset($parametersData[$key][$key2]);
                $parametersData[$newKey] = $value2;
            }

            if ($parametersData[$key] === []) {
                unset($parametersData[$key]);
            }
        }

        return $parametersData;
    }
}
