<?php declare(strict_types=1);

namespace Symplify\SymfonySecurityVoters\Tests\Adapter\Nette;

use Nette\Application\Application;
use Nette\DI\Container;
use PHPUnit\Framework\TestCase;

final class ComleteTest extends TestCase
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var Application
     */
    private $application;

    protected function setUp()
    {
        $this->container = (new ContainerFactory())->create();
        $this->application = $this->container->getByType(Application::class);
    }

    /**
     * @expectedException \Nette\Application\AbortException
     */
    public function testShouldFail()
    {
        // this should fail on SomeVoter voter
        $this->application->run();
    }
}
