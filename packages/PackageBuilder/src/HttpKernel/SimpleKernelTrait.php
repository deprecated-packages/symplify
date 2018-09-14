<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\HttpKernel;

use Nette\Utils\Strings;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

trait SimpleKernelTrait
{
    /**
     * @var string|null
     */
    private $undescoredKernelName;

    /**
     * Default constructor to disable caching, used mainly for tests.
     */
    public function __construct()
    {
        parent::__construct($this->getUniqueKernelKey() . random_int(1, 10000), true);
    }

    /**
     * Default method to prevent forcing using it
     * when no bundles are needed.
     *
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [];
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/' . $this->getUniqueKernelKey();
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/' . $this->getUniqueKernelKey() . '_logs';
    }

    /**
     * E.g. "TokenRunnerKernel" => "token_runner"
     */
    private function getUniqueKernelKey(): string
    {
        if ($this->undescoredKernelName !== null) {
            return $this->undescoredKernelName;
        }

        $classParts = explode('\\', self::class);

        $bareClassName = array_pop($classParts);
        $bareClassNameWithoutSuffix = Strings::substring($bareClassName, 0, -strlen('Kernel'));
        $bareClassNameWithoutSuffix = lcfirst($bareClassNameWithoutSuffix);

        $this->undescoredKernelName = Strings::replace(
            $bareClassNameWithoutSuffix,
            '#[A-Z]#',
            function (array $matches): string {
                return '_' . strtolower($matches[0]);
            }
        );

        return $this->undescoredKernelName;
    }
}
