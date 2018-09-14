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
        return sys_get_temp_dir() . '/' . $this->getUnderscoredKernelName();
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/' . $this->getUnderscoredKernelName() . '_logs';
    }

    /**
     * E.g. "TokenRunnerKernel" => "token_runner"
     */
    private function getUnderscoredKernelName(): string
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
