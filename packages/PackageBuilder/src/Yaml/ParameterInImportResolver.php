<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Yaml;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symplify\PackageBuilder\Composer\VendorDirProvider;
use function Safe\getcwd;

/**
 * This service resolve parameters in import section, e.g:
 *
 * # config.yml
 * imports:
 *      - { resource: '%vendor_dir%/symplify/easy-coding-standard/psr2.yml' }
 *
 * to their absolute path. That way you can load always from the same file independent on relative location.
 */
final class ParameterInImportResolver
{
    /**
     * @var string
     */
    private const IMPORTS_KEY = 'imports';

    /**
     * @var string
     */
    private const RESOURCE_KEY = 'resource';

    /**
     * @var ParameterBag
     */
    private $parameterBag;

    public function __construct()
    {
        $this->parameterBag = new ParameterBag([
            'current_working_dir' => getcwd(),
            'vendor_dir' => VendorDirProvider::provide(),
            # aliases for simple use
            'cwd' => getcwd(),
            'vendor' => VendorDirProvider::provide(),
        ]);
    }

    /**
     * @param mixed[] $configuration
     * @return mixed[]
     */
    public function process(array $configuration): array
    {
        if (! isset($configuration[self::IMPORTS_KEY])) {
            return $configuration;
        }

        foreach ($configuration[self::IMPORTS_KEY] as $key => $import) {
            $configuration[self::IMPORTS_KEY][$key][self::RESOURCE_KEY] = $this->parameterBag->resolveValue(
                $import[self::RESOURCE_KEY]
            );
        }

        return $configuration;
    }
}
