<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Adapter\Nette\Routing;

use Nette;
use Nette\Application\IRouter;
use Nette\Application\Request;
use Nette\Http\IRequest;
use Nette\Http\Url;
use OdbavTo\PresenterRoute\RouteException;

final class PresenterRoute implements IRouter
{
    /**
     * @var string
     */
    private $route;

    /**
     * @var string
     */
    private $presenterClassName;

    /**
     * @var array
     */
    private $supportedHttpMethods = [];

    /**
     * @param string $route
     * @param string $presenterClassName
     * @param string[] $supportedHttpMethods
     */
    public function __construct(string $route, string $presenterClassName , array $supportedHttpMethods = [IRequest::GET])
    {
        $this->route = $route;
        $this->presenterClassName = $presenterClassName;
        $this->supportedHttpMethods = $supportedHttpMethods;
    }

    private function normalizePath(string $path): string
    {
        return trim($path, '/');
    }

    public function match(IRequest $httpRequest): ?Request
    {
        $path = $this->normalizePath($httpRequest->getUrl()->getPath());
        if (! $this->isHttpMethodSupported($httpRequest->getMethod())) {
            return NULL;
        }

        $route = $this->normalizePath($this->route);
        $route = str_replace('/', '\/', $route);

        // use named sub-patterns to match params
        $routeRegex = preg_replace('/<[\w_-]+>/', '(?$0[\w_-]+)', $route);
        $routeRegex = '@^' . $routeRegex . '$@';
        $result = preg_match($routeRegex, $path, $matches);
        if (!$result) {
            return NULL;
        }

        $params = $httpRequest->getQuery();
        if (is_array($matches) && count($matches) > 1) {
            $params += array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        }

        return new Request(
            $this->presenterClassName,
            $httpRequest->getMethod(),
            $params,
            $httpRequest->getPost(),
            $httpRequest->getFiles(),
            [Request::SECURED => $httpRequest->isSecured()]
        );
    }

    public function constructUrl(Request $appRequest, Url $refUrl): ?string
    {
        $baseUrl = $refUrl->getHostUrl();

        $path = preg_replace_callback('/<([\w_-]+)>/', function ($matches) use ($appRequest)
        {
            if (!isset($matches[1])) {
                throw new RouteException('There is something very wrong with matches: ' . var_export($matches, false));
            }
            $match = $matches[1];
            $value = $appRequest->getParameter($match);
            if ($value) {
                return $value;
            }

            throw new RouteException('Parameter ' . $match . ' is not defined in Request.');
        }, $this->route);

        if ($path === null) {
            throw new RouteException(
                'There was an error on constructing url with: ' . $this->route
            );
        }

        return $baseUrl . '/' . $path;

    }
    private function isHttpMethodSupported(string $httpMethod): bool
    {
        if (is_array($this->supportedHttpMethods)) {
            return in_array($httpMethod, $this->supportedHttpMethods, TRUE);
        }
        return TRUE;
    }
}
