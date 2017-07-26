<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Adapter\Nette\Application;

use Contributte\Events\Bridges\Application\Event\ApplicationEvents;
use Contributte\Events\Bridges\Application\Event\ErrorEvent;
use Contributte\Events\Bridges\Application\Event\RequestEvent;
use Contributte\Events\Bridges\Application\Event\ResponseEvent;
use Contributte\Events\Bridges\Application\Event\ShutdownEvent;
use Contributte\Events\Bridges\Application\Event\StartupEvent;
use Nette\Application\Application;
use Nette\Application\ApplicationException;
use Nette\Application\BadRequestException;
use Nette\Application\InvalidPresenterException;
use Nette\Application\IPresenter;
use Nette\Application\IPresenterFactory;
use Nette\Application\IResponse as ApplicationResponse;
use Nette\Application\IRouter;
use Nette\Application\Request;
use Nette\Application\Responses\ForwardResponse;
use Nette\Application\Responses\TextResponse;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symplify\SymbioticController\Adapter\Nette\Event\CallablePresenterEvent;
use Throwable;

final class InvokablePresenterAwareApplication extends Application
{
    /**
     * @var Request[]
     */
    private $requests = [];

    /**
     * @var callable|IPresenter
     */
    private $presenter;

    /**
     * @var IRequest
     */
    private $httpRequest;

    /**
     * @var IResponse
     */
    private $httpResponse;

    /**
     * @var IPresenterFactory
     */
    private $presenterFactory;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        IPresenterFactory $presenterFactory,
        IRouter $router,
        IRequest $httpRequest,
        IResponse $httpResponse,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->httpRequest = $httpRequest;
        $this->httpResponse = $httpResponse;
        $this->presenterFactory = $presenterFactory;
        $this->eventDispatcher = $eventDispatcher;

        parent::__construct($presenterFactory, $router, $httpRequest, $httpResponse);
    }

    public function run(): void
    {
        try {
            $this->eventDispatcher->dispatch(
                ApplicationEvents::ON_STARTUP,
                new StartupEvent($this)
            );
            $this->processRequest($this->createInitialRequest());
            $this->eventDispatcher->dispatch(
                ApplicationEvents::ON_SHUTDOWN,
                new ShutdownEvent($this)
            );
        } catch (Throwable $exception) {
            $this->dispatchException($exception);
        }
    }

    public function processRequest(Request $request): void
    {
        process:
        $this->ensureApplicationIsNotCycled();

        $this->requests[] = $request;
        $this->eventDispatcher->dispatch(
            ApplicationEvents::ON_REQUEST,
            new RequestEvent($this, $request)
        );

        $this->ensureRequestIsValid($request);

        try {
            $this->presenter = $this->presenterFactory->createPresenter($request->getPresenterName());
        } catch (InvalidPresenterException $exception) {
            throw count($this->requests) > 1
                ? $exception
                : new BadRequestException($exception->getMessage(), 0, $exception);
        }

        $this->eventDispatcher->dispatch(
            ApplicationEvents::ON_PRESENTER,
            new CallablePresenterEvent($this, $this->presenter)
        );

        $response = $this->processPresenterWithRequestAndReturnResponse($request);

        if ($response instanceof ForwardResponse) {
            $request = $response->getRequest();
            goto process;
        }

        $this->eventDispatcher->dispatch(
            ApplicationEvents::ON_RESPONSE,
            new ResponseEvent($this, $response)
        );

        $response->send($this->httpRequest, $this->httpResponse);
    }

    /**
     * @return null|callable|IPresenter
     */
    public function getPresenter()
    {
        return $this->presenter;
    }

    private function dispatchException(Throwable $exception): void
    {
        $this->dispatchApplicationException($exception);

        if ($this->catchExceptions && $this->errorPresenter) {
            try {
                $this->processException($exception);
                $this->eventDispatcher->dispatch(
                    ApplicationEvents::ON_SHUTDOWN,
                    new ShutdownEvent($this)
                );

                return;
            } catch (Throwable $exception) {
                $this->dispatchApplicationException($exception);
            }
        }
        $this->eventDispatcher->dispatch(ApplicationEvents::ON_SHUTDOWN, new ShutdownEvent($this, $exception));

        throw $exception;
    }

    private function dispatchApplicationException(Throwable $exception): void
    {
        $this->eventDispatcher->dispatch(ApplicationEvents::ON_ERROR, new ErrorEvent($this, $exception));
    }

    private function processPresenterWithRequestAndReturnResponse(Request $request): ApplicationResponse
    {
        if (is_callable($this->presenter)) {
            $presenter = $this->presenter;
            $response = $presenter(clone $request);
            if (is_string($response)) {
                $response = new TextResponse($response);
            }
        } else {
            $response = $this->presenter->run(clone $request);
        }

        return $response;
    }

    private function ensureApplicationIsNotCycled(): void
    {
        if (count($this->requests) > self::$maxLoop) {
            throw new ApplicationException('Too many loops detected in application life cycle.');
        }
    }

    private function ensureRequestIsValid(Request $request): void
    {
        if (! $request->isMethod($request::FORWARD) && ! strcasecmp($request->getPresenterName(), $this->errorPresenter)
        ) {
            throw new BadRequestException('Invalid request. Presenter is not achievable.');
        }
    }
}
