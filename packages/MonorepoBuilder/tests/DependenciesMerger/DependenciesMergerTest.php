<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\DependenciesMerger;

use Symplify\MonorepoBuilder\DependenciesMerger;
use Symplify\MonorepoBuilder\FileSystem\JsonFileManager;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class DependenciesMergerTest extends AbstractKernelTestCase
{
    /**
     * @var DependenciesMerger
     */
    private $dependenciesMerger;

    /**
     * @var JsonFileManager
     */
    private $jsonFileManager;

    protected function setUp(): void
    {
        $this->bootKernel(MonorepoBuilderKernel::class);

        $this->dependenciesMerger = self::$container->get(DependenciesMerger::class);
        $this->jsonFileManager = self::$container->get(JsonFileManager::class);
    }

    public function test(): void
    {
        $mergedJson = $this->dependenciesMerger->mergeJsonToRootFilePath([
            'require' => [
                'php' => '^7.1',
                'symfony/dependency-injection' => '^4.1',
            ],
            'repositories' => [
                'type' => 'vcs',
                'url' => 'https://github.com/molaux/PostgreSearchBundle.git',
            ],
        ], __DIR__ . '/Source/root.json');

        $this->assertSame(
            $mergedJson,
            $this->jsonFileManager->loadFromFilePath(__DIR__ . '/Source/expected-root.json')
        );
    }
}
