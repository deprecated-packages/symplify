<?php declare(strict_types=1);

namespace Symplify\ControllerAutowire\Controller\HttpKernel;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

trait ControllerHttpKernelTrait
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var HttpKernelInterface
     */
    private $httpKernel;

    public function setRequestStack(RequestStack $requestStack): void
    {
        $this->requestStack = $requestStack;
    }

    public function setHttpKernel(HttpKernelInterface $httpKernel): void
    {
        $this->httpKernel = $httpKernel;
    }

    /**
     * @param string[] $path
     * @param string[] $query
     */
    protected function forward(string $controller, array $path = [], array $query = []): Response
    {
        $path['_controller'] = $controller;
        $currentRequest = $this->requestStack->getCurrentRequest();
        $subRequest = $currentRequest->duplicate($query, null, $path);

        return $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }
}
