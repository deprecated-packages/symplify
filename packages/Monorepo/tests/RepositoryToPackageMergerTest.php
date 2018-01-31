<?php declare(strict_types=1);

namespace Symplify\Monorepo\Tests;

use GitWrapper\GitWorkingCopy;
use Symplify\Monorepo\RepositoryToPackageMerger;

final class RepositoryToPackageMergerTest extends AbstractContainerAwareTestCase
{
    /**
     * @var GitWorkingCopy
     */
    private $gitWorkingCopy;

    /**
     * @var RepositoryToPackageMerger
     */
    private $repositoryToPackageMerger;

    protected function setUp(): void
    {
        $this->gitWorkingCopy = $this->container->get(GitWorkingCopy::class);
        $this->repositoryToPackageMerger = $this->container->get(RepositoryToPackageMerger::class);
    }

    public function test(): void
    {
        $monorepositoryDirectory = __DIR__ . '/RepositoryToPackageMergerSource';

        $this->gitWorkingCopy->($monorepositoryDirectory);

        $this->repositoryToPackageMerger->mergeRepositoryToPackage(

        );
    }

}