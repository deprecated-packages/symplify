<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\ComposerJsonDecorator;

use Symplify\MonorepoBuilder\ComposerJsonDecorator\SortRequireComposerJsonDecorator;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class SortRequireComposerJsonDecoratorTest extends AbstractKernelTestCase
{
    /**
     * @var mixed[]
     */
    private $composerJson = [
        'required' => [
            'b' => 'v1.0.0',
            'a' => 'v1.0.0',
        ],
    ];

    /**
     * @var SortRequireComposerJsonDecorator
     */
    private $sortRequireComposerJsonDecorator;

    protected function setUp(): void
    {
        $this->bootKernel(MonorepoBuilderKernel::class);

        $this->sortRequireComposerJsonDecorator = self::$container->get(SortRequireComposerJsonDecorator::class);
    }

    public function testNoSort(): void
    {
        $decorated = $this->sortRequireComposerJsonDecorator->decorate($this->composerJson);

        $this->assertSame($this->composerJson, $decorated);
    }

    public function testSort(): void
    {
        $composerJsonWithSort = $this->composerJson + [
            'config' => ['sort-packages' => true],
        ];

        $decorated = $this->sortRequireComposerJsonDecorator->decorate($composerJsonWithSort);

        $this->assertSame([
            'required' => [
                'b' => 'v1.0.0',
                'a' => 'v1.0.0',
            ],
            'config' => [
                'sort-packages' => true,
            ],
        ], $decorated);
    }
}
