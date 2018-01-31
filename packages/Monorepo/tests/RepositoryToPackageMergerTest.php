<?php declare(strict_types=1);

namespace Symplify\Monorepo\Tests;

use GitWrapper\GitWrapper;
use Nette\Utils\FileSystem;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\Monorepo\RepositoryToPackageMerger;

final class RepositoryToPackageMergerTest extends AbstractContainerAwareTestCase
{
    /**
     * @var string
     */
    private const TEMP_MONOREPO_DIRECTORY = __DIR__ . '/RepositoryToPackageMergerSource/TempRepository';

    /**
     * @var GitWrapper
     */
    private $gitWrapper;

    /**
     * @var RepositoryToPackageMerger
     */
    private $repositoryToPackageMerger;

    protected function setUp(): void
    {
        $this->gitWrapper = $this->container->get(GitWrapper::class);
        $this->repositoryToPackageMerger = $this->container->get(RepositoryToPackageMerger::class);

        /** @var OutputInterface $output */
        $output = $this->container->get(OutputInterface::class);
        $output->setVerbosity(OutputInterface::VERBOSITY_QUIET);
    }

    protected function tearDown(): void
    {
        FileSystem::delete(self::TEMP_MONOREPO_DIRECTORY);
    }

    public function testMergeTwoPackages(): void
    {
        $this->gitWrapper->init(self::TEMP_MONOREPO_DIRECTORY);

        $this->repositoryToPackageMerger->mergeRepositoryToPackage(
            'https://github.com/Symplify/Monorepo.git',
            self::TEMP_MONOREPO_DIRECTORY,
            'packages/Monorepo'
        );

        $this->assertDirectoryNotExists(self::TEMP_MONOREPO_DIRECTORY . '/src');
        $this->assertDirectoryExists(self::TEMP_MONOREPO_DIRECTORY . '/packages/Monorepo/src');

//        $this->repositoryToPackageMerger->mergeRepositoryToPackage(
//            'https://github.com/Symplify/CodingStandard.git',
//            self::TEMP_MONOREPO_DIRECTORY,
//            'packages/CodingStandard'
//        );
//
//        $this->assertDirectoryNotExists(self::TEMP_MONOREPO_DIRECTORY . '/src');
//        $this->assertDirectoryExists(self::TEMP_MONOREPO_DIRECTORY . '/packages/CodingStandard/src');
    }
}
