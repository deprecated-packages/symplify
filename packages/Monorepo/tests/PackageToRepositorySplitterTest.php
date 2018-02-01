<?php declare(strict_types=1);

namespace Symplify\Monorepo\Tests;

use GitWrapper\GitWrapper;
use Nette\Utils\FileSystem;

/**
 * @todo
 */
final class PackageToRepositorySplitterTest extends AbstractContainerAwareTestCase
{
    /**
     * @var string
     */
    private const TEMP_MONOREPO_DIRECTORY = __DIR__ . '/PackageToRepositorySplitterSource/TempRepository';

    /**
     * @var GitWrapper
     */
    private $gitWrapper;

    protected function setUp(): void
    {
        $this->gitWrapper = $this->container->get(GitWrapper::class);
    }

    protected function tearDown(): void
    {
        FileSystem::delete(self::TEMP_MONOREPO_DIRECTORY);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test(): void
    {
        $this->gitWrapper->init(self::TEMP_MONOREPO_DIRECTORY);
    }
}
