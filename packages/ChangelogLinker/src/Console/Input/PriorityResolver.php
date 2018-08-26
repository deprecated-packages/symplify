<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Console\Input;

use Symfony\Component\Console\Input\InputInterface;
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

        $requiredOptions = ['in-packages', 'in-categories'];

        if (count(array_intersect($requiredOptions, array_keys($rawOptions))) !== count($requiredOptions)) {
            return null;
        }

        foreach (array_keys($rawOptions) as $name) {
            if ($name === 'in-packages') {
                return 'packages';
            }

            return 'categories';
        }

        return null;
    }
}
