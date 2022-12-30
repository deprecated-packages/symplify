<?php

declare(strict_types=1);

namespace Symplify\EasyParallel\ValueObject;

use Clue\React\NDJson\Decoder;
use Clue\React\NDJson\Encoder;
use Exception;
use React\ChildProcess\Process;
use React\EventLoop\LoopInterface;
use React\EventLoop\TimerInterface;
use Symplify\EasyParallel\Enum\Action;
use Symplify\EasyParallel\Enum\Content;
use Symplify\EasyParallel\Enum\ReactCommand;
use Symplify\EasyParallel\Enum\ReactEvent;
use Symplify\EasyParallel\Exception\ParallelShouldNotHappenException;
use Throwable;

/**
 * Inspired at @see https://raw.githubusercontent.com/phpstan/phpstan-src/master/src/Parallel/Process.php
 *
 * @api
 */
final class ParallelProcess
{
    private Process $process;

    private Encoder $encoder;

    /**
     * @var resource|null
     */
    private $stdErr;

    /**
     * @var callable(mixed[]) : void
     */
    private $onData;

    /**
     * @var callable(Throwable): void
     */
    private $onError;

    private ?TimerInterface $timer = null;

    public function __construct(
        private string $command,
        private LoopInterface $loop,
        private int $timetoutInSeconds
    ) {
    }

    /**
     * @param callable(mixed[] $onData) : void $onData
     * @param callable(Throwable $onError) : void $onError
     * @param callable(?int $onExit, string $output) : void $onExit
     */
    public function start(callable $onData, callable $onError, callable $onExit): void
    {
        $tmp = tmpfile();
        if ($tmp === false) {
            throw new ParallelShouldNotHappenException('Failed creating temp file.');
        }

        $this->stdErr = $tmp;
        $this->process = new Process($this->command, null, null, [
            2 => $this->stdErr,
            // todo is it fine to not have 0 and 1 FD?
        ]);
        $this->process->start($this->loop);

        $this->onData = $onData;
        $this->onError = $onError;

        $this->process->on(ReactEvent::EXIT, function ($exitCode) use ($onExit): void {
            if ($this->stdErr === null) {
                throw new ParallelShouldNotHappenException();
            }

            $this->cancelTimer();

            rewind($this->stdErr);

            /** @var string $streamContents */
            $streamContents = stream_get_contents($this->stdErr);
            $onExit($exitCode, $streamContents);

            fclose($this->stdErr);
        });
    }

    /**
     * @param mixed[] $data
     */
    public function request(array $data): void
    {
        $this->cancelTimer();
        $this->encoder->write($data);
        $this->timer = $this->loop->addTimer($this->timetoutInSeconds, function (): void {
            $onError = $this->onError;

            $errorMessage = sprintf('Child process timed out after %d seconds', $this->timetoutInSeconds);
            $onError(new Exception($errorMessage));
        });
    }

    public function quit(): void
    {
        $this->cancelTimer();
        if (! $this->process->isRunning()) {
            return;
        }

        foreach ($this->process->pipes as $pipe) {
            $pipe->close();
        }

        $this->encoder->end();
    }

    public function bindConnection(Decoder $decoder, Encoder $encoder): void
    {
        $decoder->on(ReactEvent::DATA, function (array $json): void {
            $this->cancelTimer();
            if ($json[ReactCommand::ACTION] !== Action::RESULT) {
                return;
            }

            $onData = $this->onData;
            $onData($json[Content::RESULT]);
        });
        $this->encoder = $encoder;

        $decoder->on(ReactEvent::ERROR, function (Throwable $throwable): void {
            $onError = $this->onError;
            $onError($throwable);
        });

        $encoder->on(ReactEvent::ERROR, function (Throwable $throwable): void {
            $onError = $this->onError;
            $onError($throwable);
        });
    }

    private function cancelTimer(): void
    {
        if ($this->timer === null) {
            return;
        }

        $this->loop->cancelTimer($this->timer);
        $this->timer = null;
    }
}
