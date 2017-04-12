<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Adapter\Nette\Routing;

use Nette\Application\IRouter;
use Nette\Application\Request;
use Nette\Http\IRequest;
use Nette\Http\Url;
use OdbavTo\PresenterRoute\RouteException;
use Symplify\SymbioticController\Exception\MissingClassException;
use Symplify\SymbioticController\Exception\MissingInvokeMethodException;

final class PresenterRoute implements IRouter
{
    /**
     * @var string
     */
    private $mask;

    /**
     * @var string
     */
    private $presenterClass;

    public function __construct(string $mask, string $presenterClass)
    {
        $this->mask = $mask;
        $this->setPresenterClass($presenterClass);
    }

    public function match(IRequest $httpRequest): ?Request
    {
        $urlScript = $httpRequest->getUrl();
        $path = $this->normalizePath($urlScript->getPath());

        $mask = $this->normalizePath($this->mask);
        $mask = str_replace('/', '\/', $mask);

        // use named sub-patterns to match params
        $routeRegex = preg_replace('/<[\w_-]+>/', '(?$0[\w_-]+)', $mask);
        $routeRegex = '@^' . $routeRegex . '$@';
        $result = preg_match($routeRegex, $path, $matches);
        if (! $result) {
            return null;
        }

        $params = $httpRequest->getQuery();

        if (is_array($matches) && count($matches) > 1) {
            $params += array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        }

        // hotfix
        foreach ($params as $key => $param) {
            $params[$key] = is_numeric($param) ? (int) $param : $param;
        }

        return new Request(
            $this->presenterClass,
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

        $path = preg_replace_callback('/<([\w_-]+)>/', function ($matches) use ($appRequest) {
            if (! isset($matches[1])) {
                throw new RouteException(
                    'There is something very wrong with matches: ' . var_export($matches, false)
                );
            }

            $match = $matches[1];
            $value = $appRequest->getParameter($match);
            if ($value) {
                return $value;
            }

            throw new RouteException('Parameter ' . $match . ' is not defined in Request.');
        }, $this->mask);

        if ($path === null) {
            throw new RouteException(
                'There was an error on constructing url with: ' . $this->mask
            );
        }

        return $baseUrl . '/' . $path;
    }

    private function normalizePath(string $path): string
    {
        return trim($path, '/');
    }

    private function setPresenterClass(string $presenterClass): void
    {
        $this->presenterClass = $presenterClass;
        $this->ensureClassExistsAndIsInvokable($presenterClass);
    }

    private function ensureClassExistsAndIsInvokable(string $class): void
    {
        if (! class_exists($class)) {
            throw new MissingClassException(sprintf(
                'Presenter class "%s" was not found.',
                $class
            ));
        }

        if (! method_exists($class, '__invoke')) {
            throw new MissingInvokeMethodException(sprintf(
                'Presenter class "%s" is missing __invoke() method.',
                $class
            ));
        }
    }
}
