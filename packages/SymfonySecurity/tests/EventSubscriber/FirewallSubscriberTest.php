<?php declare(strict_types=1);

namespace Symplify\SymfonySecurity\Tests\EventSubscriber;

use Nette\Application\Application;
use Nette\Application\ForbiddenRequestException;
use Nette\Application\IPresenter;
use Nette\Application\Request as ApplicationRequest;
use Nette\Http\Request;
use Nette\Http\UrlScript;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\PresenterCreatedEvent;
use Symplify\SymfonySecurity\Contract\Http\FirewallHandlerInterface;
use Symplify\SymfonySecurity\Contract\Http\FirewallMapInterface;
use Symplify\SymfonySecurity\EventSubscriber\FirewallSubscriber;

final class FirewallSubscriberTest extends TestCase
{
    /**
     * @var FirewallSubscriber
     */
    private $firewall;

    protected function setUp()
    {
        $listenerMock = $this->prophesize(FirewallHandlerInterface::class);
        $listenerMock->handle(Argument::cetera())->willReturn(function () {
            throw new ForbiddenRequestException;
        });

        $firewallMapMock = $this->prophesize(FirewallMapInterface::class);
        $firewallMapMock->getListeners(Argument::type(Request::class))->willReturn(
            [[$listenerMock->reveal()], '']
        );

        $request = new Request((new UrlScript)->setScriptPath('admin/script.php'));
        $this->firewall = new FirewallSubscriber($firewallMapMock->reveal(), $request);
    }

    public function testGetSubscribedEvents()
    {
        $this->assertSame(
            [PresenterCreatedEvent::NAME => 'onPresenter'],
            $this->firewall->getSubscribedEvents()
        );
    }

    public function testOnPresenter()
    {
        $requestMock = $this->prophesize(ApplicationRequest::class);
        $requestMock->getParameters()->willReturn(['parameter' => 'value']);

        $applicationPresenterEventMock = new PresenterCreatedEvent(
            $this->prophesize(Application::class)->reveal(),
            $this->prophesize(IPresenter::class)->reveal()
        );

        $this->firewall->onPresenter($applicationPresenterEventMock);
    }
}
