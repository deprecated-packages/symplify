<?php declare(strict_types=1);

namespace Symplify\ControllerAutowire\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symplify\ControllerAutowire\Contract\DependencyInjection\ControllerClassMapInterface;
use Symplify\ControllerAutowire\Controller\ControllerTrait;
use Symplify\ControllerAutowire\Controller\Doctrine\ControllerDoctrineTrait;
use Symplify\ControllerAutowire\Controller\Form\ControllerFormTrait;
use Symplify\ControllerAutowire\Controller\HttpKernel\ControllerHttpKernelTrait;
use Symplify\ControllerAutowire\Controller\Routing\ControllerRoutingTrait;
use Symplify\ControllerAutowire\Controller\Security\ControllerSecurityTrait;
use Symplify\ControllerAutowire\Controller\Serializer\ControllerSerializerTrait;
use Symplify\ControllerAutowire\Controller\Session\ControllerFlashTrait;
use Symplify\ControllerAutowire\Controller\Templating\ControllerRenderTrait;

final class AutowireControllerDependenciesPass implements CompilerPassInterface
{
    /**
     * @var ControllerClassMapInterface
     */
    private $controllerClassMap;

    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * @var array[]
     */
    private $traitsToSettersToServiceNameList = [
        ControllerFlashTrait::class => [
            'setSession' => 'session',
        ],
        ControllerDoctrineTrait::class => [
            'setDoctrine' => 'doctrine',
        ],
        ControllerRoutingTrait::class => [
            'setRouter' => 'router',
        ],
        ControllerHttpKernelTrait::class => [
            'setHttpKernel' => 'http_kernel',
            'setRequestStack' => 'request_stack',
        ],
        ControllerSerializerTrait::class => [
            'setSerializer' => 'serializer',
        ],
        ControllerSecurityTrait::class => [
            'setAuthorizationChecker' => 'security.authorization_checker',
            'setTokenStorage' => 'security.token_storage',
            'setCsrfTokenManager' => 'security.csrf.token_manager',
        ],
        ControllerRenderTrait::class => [
            'setTemplating' => 'templating',
            'setTwig' => 'twig',
        ],
        ControllerFormTrait::class => [
            'setFormFactory' => 'form.factory',
        ],
    ];

    public function __construct(ControllerClassMapInterface $controllerClassMap)
    {
        $this->controllerClassMap = $controllerClassMap;
    }

    public function process(ContainerBuilder $containerBuilder)
    {
        $this->containerBuilder = $containerBuilder;

        foreach ($this->controllerClassMap->getControllers() as $serviceId => $className) {
            $controllerDefinition = $containerBuilder->getDefinition($serviceId);
            $this->autowireControllerTraits($controllerDefinition);
        }
    }

    private function autowireControllerTraits(Definition $controllerDefinition)
    {
        $usedTraits = class_uses($controllerDefinition->getClass());

        foreach ($this->traitsToSettersToServiceNameList as $traitClass => $setterToServiceNames) {
            if (! $this->isTraitIncluded($traitClass, $usedTraits)) {
                continue;
            }

            foreach ($setterToServiceNames as $setter => $serviceName) {
                if (! $this->containerBuilder->has($serviceName)) {
                    continue;
                }

                $controllerDefinition->addMethodCall($setter, [new Reference($serviceName)]);
            }
        }
    }

    private function isTraitIncluded(string $traitClass, array $usedTraits) : bool
    {
        if (array_key_exists($traitClass, $usedTraits)) {
            return true;
        }

        if (isset($usedTraits[ControllerTrait::class])) {
            return true;
        }

        return false;
    }
}
