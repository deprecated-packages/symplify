<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Application;

use Exception;
use Nette;
use Nette\Application\AbortException;
use Nette\Application\Application;
use Nette\Application\ApplicationException;
use Nette\Application\BadRequestException;
use Nette\Application\InvalidPresenterException;
use Nette\Application\IPresenter;
use Nette\Application\IPresenterFactory;
use Nette\Application\IRouter;
use Nette\Application\Request;
use Nette\Application\Responses;
use Nette\Application\UI;
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
     * @var int
     */
    public static $maxLoop = 20;

    /**
     * @var bool enable fault barrier?
     */
    public $catchExceptions;

    /**
     * @var string
     */
    public $errorPresenter;

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
     * @var IRouter
     */
    private $router;

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
        $this->router = $router;
        $this->eventDispatcher = $eventDispatcher;
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

    public function createInitialRequest(): Request
    {
        $request = $this->router->match($this->httpRequest);
        if (! $request instanceof Request) {
            throw new BadRequestException('No route for HTTP request.');
        }

        return $request;
    }

    public function processRequest(Request $request): void
    {
        process:
        if (count($this->requests) > self::$maxLoop) {
            throw new ApplicationException('Too many loops detected in application life cycle.');
        }

        $this->requests[] = $request;
        $this->eventDispatcher->dispatch(
            RequestRecievedEvent::class, new RequestRecievedEvent($this, $request)
        );

        if (! $request->isMethod($request::FORWARD) && ! strcasecmp($request->getPresenterName(), $this->errorPresenter)
        ) {
            throw new BadRequestException('Invalid request. Presenter is not achievable.');
        }

        try {
            $this->presenter = $this->presenterFactory->createPresenter($request->getPresenterName());
        } catch (InvalidPresenterException $e) {
            throw count($this->requests) > 1 ? $e : new BadRequestException($e->getMessage(), 0, $e);
        }
        $this->eventDispatcher->dispatch(
            ApplicationResponseEvent::class, new PresenterCreatedEvent($this, $this->presenter)
        );

        if (is_callable($this->presenter)) {
            $presenter = $this->presenter;
            $response = $presenter(clone $request);
        } else {
            $response = $this->presenter->run(clone $request);
        }

        if ($response instanceof Responses\ForwardResponse) {
            $request = $response->getRequest();
            goto process;
        } elseif ($response) {
            $this->eventDispatcher->dispatch(
                ApplicationResponseEvent::class, new ApplicationResponseEvent($this, $response)
            );
            $response->send($this->httpRequest, $this->httpResponse);
        }
    }

    /**
     * @param Exception|Throwable $e
     */
    public function processException($e): void
    {
        if (! $e instanceof BadRequestException && $this->httpResponse instanceof Nette\Http\Response) {
            $this->httpResponse->warnOnBuffer = FALSE;
        }
        if (! $this->httpResponse->isSent()) {
            $this->httpResponse->setCode($e instanceof BadRequestException ? ($e->getHttpCode() ?: 404) : 500);
        }

        $args = ['exception' => $e, 'request' => end($this->requests) ?: NULL];
        if ($this->presenter instanceof UI\Presenter) {
            try {
                $this->presenter->forward(":$this->errorPresenter:", $args);
            } catch (AbortException $foo) {
                $this->processRequest($this->presenter->getLastCreatedRequest());
            }
        } else {
            $this->processRequest(new Request($this->errorPresenter, Request::FORWARD, $args));
        }
    }

    /**
     * Returns all processed requests.
     * @return Request[]
     */
    public function getRequests(): array
    {
        return $this->requests;
    }

    public function getPresenter(): ?IPresenter
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
        $this->eventDispatcher->dispatch(
            ApplicationShutdownEvent::class,
            new ApplicationShutdownEvent($this, $exception)
        );

        throw $exception;
    }

    private function dispatchApplicationException(Throwable $exception): void
    {
        $this->eventDispatcher->dispatch(
            ApplicationErrorEvent::class, new ApplicationErrorEvent($this, $exception)
        );
    }
}
