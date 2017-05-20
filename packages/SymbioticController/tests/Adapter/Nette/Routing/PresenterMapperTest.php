<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Tests\Adapter\Nette\Routing;

use Nette\InvalidStateException;
use PHPUnit\Framework\TestCase;
use Symplify\SymbioticController\Adapter\Nette\Routing\PresenterMapper;

final class PresenterMapperTest extends TestCase
{
    /**
     * @var PresenterMapper
     */
    private $presenterMapper;

    protected function setUp(): void
    {
        $this->presenterMapper = new PresenterMapper;
    }

    public function testSetMappingError(): void
    {
        $this->expectException(InvalidStateException::class);
        $this->presenterMapper->setMapping([
            '*' => ['*', '*'],
        ]);
    }

    public function testDetectPresenterClassFromPresenterName(): void
    {
        $this->assertSame(
            'ModuleModule\FooModule\BarPresenter',
            $this->presenterMapper->detectPresenterClassFromPresenterName('Module:Foo:Bar')
        );

        $this->presenterMapper->setMapping([
            '*' => ['', '*', '*'],
        ]);

        $this->assertSame(
            'Module\Foo\Bar',
            $this->presenterMapper->detectPresenterClassFromPresenterName('Module:Foo:Bar')
        );
    }
}
