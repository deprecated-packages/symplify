<?php declare(strict_types=1);

namespace Symplify\ControllerAutowire\Controller\Templating;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Twig_Environment;

trait ControllerRenderTrait
{
    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var Twig_Environment
     */
    private $twig;

    public function setTemplating(EngineInterface $templating): void
    {
        $this->templating = $templating;
    }

    public function setTwig(Twig_Environment $twig): void
    {
        $this->twig = $twig;
    }

    protected function renderView(string $view, array $parameters = []): string
    {
        if ($this->templating) {
            return $this->templating->render($view, $parameters);
        }

        return $this->twig->render($view, $parameters);
    }

    protected function render(string $view, array $parameters = [], ?Response $response = null): Response
    {
        if ($this->templating) {
            return $this->templating->renderResponse($view, $parameters, $response);
        }

        if ($response === null) {
            $response = new Response;
        }

        return $response->setContent($this->twig->render($view, $parameters));
    }

    protected function stream(
        string $view,
        array $parameters = [],
        ?StreamedResponse $response = null
    ): StreamedResponse {
        if ($this->templating) {
            $callback = function () use ($view, $parameters) {
                $this->templating->stream($view, $parameters);
            };
        } else {
            $callback = function () use ($view, $parameters) {
                $this->twig->display($view, $parameters);
            };
        }

        if ($response === null) {
            return new StreamedResponse($callback);
        }

        $response->setCallback($callback);

        return $response;
    }
}
