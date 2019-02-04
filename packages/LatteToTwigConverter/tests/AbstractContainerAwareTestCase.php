<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symplify\LatteToTwigConverter\DependencyInjection\ContainerFactory;

abstract class AbstractContainerAwareTestCase extends TestCase
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param mixed[]    $data
     * @param int|string $dataName
     */
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        $this->container = (new ContainerFactory())->create();

        parent::__construct($name, $data, $dataName);
    }
}
