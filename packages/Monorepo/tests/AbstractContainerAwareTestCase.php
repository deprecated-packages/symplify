<?php declare(strict_types=1);

namespace Symplify\Monorepo\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\Monorepo\DependencyInjection\ContainerFactory;

abstract class AbstractContainerAwareTestCase extends TestCase
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ContainerInterface
     */
    private static $cachedContainer;

    /**
     * @param mixed[] $data
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        if (! self::$cachedContainer) {
            self::$cachedContainer = (new ContainerFactory())->create();
        }

        $this->container = self::$cachedContainer;

        /** @var OutputInterface $output */
//        $output = $this->container->get(OutputInterface::class);
//        $output->setVerbosity(OutputInterface::VERBOSITY_QUIET);

        parent::__construct($name, $data, $dataName);
    }
}
