<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\ComposerJsonDecorator;

use Symplify\MonorepoBuilder\ComposerJsonDecorator\SortComposerJsonDecorator;
use Symplify\MonorepoBuilder\Tests\AbstractContainerAwareTestCase;

final class SortComposerJsonDecoratorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var mixed[]
     */
    private $composerJson = [
        'random-this' => [],
        'autoload-dev' => [],
        'autoload' => [],
        'random-that' => [],
        'require-dev' => [],
        'require' => [],
    ];

    public function test(): void
    {
        /** @var SortComposerJsonDecorator $sortComposerJsonDecorator */
        $sortComposerJsonDecorator = $this->container->get(SortComposerJsonDecorator::class);

        $sortedComposerJson = $sortComposerJsonDecorator->decorate($this->composerJson);
        $this->assertSame(
            ['random-this', 'random-that', 'require', 'require-dev', 'autoload', 'autoload-dev'],
            array_keys($sortedComposerJson)
        );
    }
}
