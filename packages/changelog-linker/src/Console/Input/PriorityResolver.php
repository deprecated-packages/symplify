<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Console\Input;

use Symfony\Component\Console\Input\InputInterface;
use Symplify\ChangelogLinker\ValueObject\Option;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;

final class PriorityResolver
{
    /**
     * @var PrivatesAccessor
     */
    private $privatesAccessor;

    public function __construct()
    {
        $this->privatesAccessor = new PrivatesAccessor();
    }

    /**
     * Detects the order in which "--in-packages" and "--in-categories" are both called.
     * The first has a priority.
     */
    public function resolveFromInput(InputInterface $input): ?string
    {
        $rawOptions = $this->privatesAccessor->getPrivateProperty($input, 'options');

        $requiredOptions = [Option::IN_PACKAGES, Option::IN_CATEGORIES];

        $optionNames = array_keys($rawOptions);
        $usedOptions = array_intersect($requiredOptions, $optionNames);

        if (count($usedOptions) !== count($requiredOptions)) {
            return null;
        }

        foreach ($optionNames as $optionName) {
            if ($optionName === Option::IN_PACKAGES) {
                return 'packages';
            }

            return 'categories';
        }

        return null;
    }
}
