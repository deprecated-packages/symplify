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

    public function testShouldFail()
    {
        // this should fail on voters
        $this->application->run();
    }

    public function testShouldPass()
    {
        // this should fail on voters
//        $this->application->run();
    }
}
