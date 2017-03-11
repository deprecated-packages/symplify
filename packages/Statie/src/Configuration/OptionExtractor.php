<?php declare(strict_types=1);

namespace Symplify\Statie\Configuration;

final class OptionExtractor
{

    private function extractPostRoute(array $options, string $optionName): array
    {
        if (! isset($options['configuration'][$optionName])) {
            return $options;
        }

        $this->setPostRoute($options['configuration'][$optionName]);
        unset($options['configuration'][$optionName]);

        return $options;
    }

}
