<?php declare(strict_types=1);

namespace Symplify\Statie\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symplify\Statie\DependencyInjection\ContainerFactory;

abstract class AbstractContainerAwareTestCase extends TestCase
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ContainerInterface|null
     */
    private static $cachedContainer;

    /**
     * @param mixed[]    $data
     * @param int|string $dataName
     */
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        if (self::$cachedContainer === null) {
            self::$cachedContainer = (new ContainerFactory())->create();
        }

        $this->container = self::$cachedContainer;

        parent::__construct($name, $data, $dataName);
    }
}
