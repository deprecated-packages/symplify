<?php declare(strict_types=1);

namespace Symplify\ServiceDefinitionDecorator\Tests\Adapter\Symfony\Source;

use Nette\Utils\Finder;
use Symfony\Component\Console\Command\Command;

final class SomeCommand extends Command implements DummyServiceAwareInterface
{
    /**
     * @var Finder
     */
    private $finder;

    /**
     * @var DummyService
     */
    private $dummyService;

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;

        parent::__construct();
    }

    public function setDummyService(DummyService $dummyService)
    {
        $this->dummyService = $dummyService;
    }

    public function getFinder() : Finder
    {
        return $this->finder;
    }

    public function getDummyService(): DummyService
    {
        return $this->dummyService;
    }

    protected function configure()
    {
        $this->setName('some_command');
    }
}
