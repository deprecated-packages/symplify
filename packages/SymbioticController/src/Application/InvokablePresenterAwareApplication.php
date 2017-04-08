<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Application;

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
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\ApplicationErrorEvent;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\ApplicationResponseEvent;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\ApplicationShutdownEvent;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\ApplicationStartupEvent;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\PresenterCreatedEvent;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\RequestRecievedEvent;
use Throwable;

final class InvokablePresenterAwareApplication extends Application
{
    /**
     * @var Request[]
     */
    private $requests = [];

    /**
     * @var IPresenter|callable|null
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
                ApplicationStartupEvent::class, new ApplicationStartupEvent($this)
            );
            $this->processRequest($this->createInitialRequest());
            $this->eventDispatcher->dispatch(
                ApplicationShutdownEvent::class, new ApplicationShutdownEvent($this)
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
            RequestRecievedEvent::class, new RequestRecievedEvent($this, $request)
        );

        $this->ensureRequestIsValid($request);

        try {
            $this->presenter = $this->presenterFactory->createPresenter($request->getPresenterName());
        } catch (InvalidPresenterException $exception) {
            throw count($this->requests) > 1
                ? $exception
                : new BadRequestException($exception->getMessage(), 0, $exception);
        }

        $this->dispatchApplicationResponseEvent();

        $response = $this->processPresenterWithRequestAndReturnResponse($request);

        if ($response instanceof ForwardResponse) {
            $request = $response->getRequest();
            goto process;
        }

        $this->eventDispatcher->dispatch(
            ApplicationResponseEvent::class, new ApplicationResponseEvent($this, $response)
        );
        $response->send($this->httpRequest, $this->httpResponse);
    }

    /**
     * @return callable|IPresenter|null
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
                    ApplicationShutdownEvent::class, new ApplicationShutdownEvent($this)
                );

                return;
            } catch (Throwable $exception) {
                $this->dispatchApplicationException($exception);
            }
        }
        $this->dispatchApplicationShutdownException($exception);

        throw $exception;
    }

    private function dispatchApplicationResponseEvent(): void
    {
        $this->eventDispatcher->dispatch(
            PresenterCreatedEvent::class, new PresenterCreatedEvent($this, $this->presenter)
        );
    }

    private function dispatchApplicationException(Throwable $exception): void
    {
        $this->eventDispatcher->dispatch(
            ApplicationErrorEvent::class, new ApplicationErrorEvent($this, $exception)
        );
    }

    private function dispatchApplicationShutdownException(Throwable $exception): void
    {
        $this->eventDispatcher->dispatch(
            ApplicationShutdownEvent::class, new ApplicationShutdownEvent($this, $exception)
        );
    }

    private function processPresenterWithRequestAndReturnResponse(Request $request): ApplicationResponse
    {
        if (is_callable($this->presenter)) {
            $presenter = $this->presenter;
            $response = $presenter(clone $request);
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
