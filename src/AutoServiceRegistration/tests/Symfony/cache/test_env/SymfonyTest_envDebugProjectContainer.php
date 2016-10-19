<?php

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;

/**
 * SymfonyTest_envDebugProjectContainer.
 *
 * This class has been auto-generated
 * by the Symfony Dependency Injection Component.
 */
class SymfonyTest_envDebugProjectContainer extends Container
{
    private $parameters;
    private $targetDirs = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
        $dir = __DIR__;
        for ($i = 1; $i <= 5; ++$i) {
            $this->targetDirs[$i] = $dir = dirname($dir);
        }
        $this->parameters = $this->getDefaultParameters();

        $this->services = array();
        $this->methodMap = array(
            'symplify.autoserviceregistration.tests.symfony.completetestsource.anothercontroller' => 'getSymplify_Autoserviceregistration_Tests_Symfony_Completetestsource_AnothercontrollerService',
        );

        $this->aliases = array();
    }

    /**
     * {@inheritdoc}
     */
    public function compile()
    {
        throw new LogicException('You cannot compile a dumped frozen container.');
    }

    /**
     * {@inheritdoc}
     */
    public function isFrozen()
    {
        return true;
    }

    /**
     * Gets the 'symplify.autoserviceregistration.tests.symfony.completetestsource.anothercontroller' service.
     *
     * This service is autowired.
     *
     * @return \Symplify\AutoServiceRegistration\Tests\Symfony\CompleteTestSource\AnotherController A Symplify\AutoServiceRegistration\Tests\Symfony\CompleteTestSource\AnotherController instance
     */
    protected function getSymplify_Autoserviceregistration_Tests_Symfony_Completetestsource_AnothercontrollerService()
    {
        return $this->services['symplify.autoserviceregistration.tests.symfony.completetestsource.anothercontroller'] = new \Symplify\AutoServiceRegistration\Tests\Symfony\CompleteTestSource\AnotherController();
    }

    /**
     * {@inheritdoc}
     */
    public function getParameter($name)
    {
        $name = strtolower($name);

        if (!(isset($this->parameters[$name]) || array_key_exists($name, $this->parameters))) {
            throw new InvalidArgumentException(sprintf('The parameter "%s" must be defined.', $name));
        }

        return $this->parameters[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function hasParameter($name)
    {
        $name = strtolower($name);

        return isset($this->parameters[$name]) || array_key_exists($name, $this->parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function setParameter($name, $value)
    {
        throw new LogicException('Impossible to call set() on a frozen ParameterBag.');
    }

    /**
     * {@inheritdoc}
     */
    public function getParameterBag()
    {
        if (null === $this->parameterBag) {
            $this->parameterBag = new FrozenParameterBag($this->parameters);
        }

        return $this->parameterBag;
    }

    /**
     * Gets the default parameters.
     *
     * @return array An array of the default parameters
     */
    protected function getDefaultParameters()
    {
        return array(
            'kernel.root_dir' => $this->targetDirs[2],
            'kernel.environment' => 'test_env',
            'kernel.debug' => true,
            'kernel.name' => 'Symfony',
            'kernel.cache_dir' => __DIR__,
            'kernel.logs_dir' => ($this->targetDirs[2].'/logs'),
            'kernel.bundles' => array(
                'SymplifyAutoServiceRegistrationBundle' => 'Symplify\\AutoServiceRegistration\\Symfony\\SymplifyAutoServiceRegistrationBundle',
            ),
            'kernel.charset' => 'UTF-8',
            'kernel.container_class' => 'SymfonyTest_envDebugProjectContainer',
        );
    }
}
