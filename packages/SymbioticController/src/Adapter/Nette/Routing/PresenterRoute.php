<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Adapter\Nette\Routing;

use Nette\Application\IRouter;
use Nette\Application\Request;
use Nette\Application\Routers\Route;
use Nette\Http\IRequest;
use Nette\Http\Url;
use Symplify\SymbioticController\Exception\MissingClassException;
use Symplify\SymbioticController\Exception\MissingInvokeMethodException;

final class PresenterRoute implements IRouter
{
    /**
     * @var string
     */
    private $netteRoute;

    public function __construct(string $mask, string $presenterClass)
    {
        $this->ensureClassExistsAndIsInvokable($presenterClass);

        $this->netteRoute = new Route($mask, [
            'presenter' => $presenterClass,
            'action' =>'__invoke'
        ]);
    }

    public function match(IRequest $httpRequest): ?Request
    {
        $appRequest = $this->netteRoute->match($httpRequest);
        if ($appRequest) {
            $this->removeActionParameter($appRequest);
            return $appRequest;
        }

        return null;
    }

    public function constructUrl(Request $appRequest, Url $refUrl): ?string
    {
        return $this->netteRoute->constructUrl($appRequest, $refUrl);

//        $baseUrl = $refUrl->getHostUrl();
//
//        $path = preg_replace_callback('/<([\w_-]+)>/', function ($matches) use ($appRequest) {
//            if (! isset($matches[1])) {
//                throw new RouteException(
//                    'There is something very wrong with matches: ' . var_export($matches, false)
//                );
//            }
//
//            $match = $matches[1];
//            $value = $appRequest->getParameter($match);
//            if ($value) {
//                return $value;
//            }
//
//            throw new RouteException('Parameter ' . $match . ' is not defined in Request.');
//        }, $this->mask);
//
//        if ($path === null) {
//            throw new RouteException(
//                'There was an error on constructing url with: ' . $this->mask
//            );
//        }
//
//        return $baseUrl . '/' . $path;
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

    private function removeActionParameter(Request $appRequest): void
    {
        $parameters = $appRequest->getParameters();
        unset($parameters['action']);
        $appRequest->setParameters($parameters);
    }
}
