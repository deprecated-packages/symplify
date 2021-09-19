<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Parallel\ValueObject;

use React\ChildProcess\Process;
use React\EventLoop\LoopInterface;
use React\EventLoop\TimerInterface;
use React\Stream\ReadableStreamInterface;
use React\Stream\WritableStreamInterface;
use Symplify\EasyCodingStandard\Parallel\Enum\Action;
use Symplify\EasyCodingStandard\Parallel\Exception\ParallelShouldNotHappenException;

/**
 * Inspired at @see https://raw.githubusercontent.com/phpstan/phpstan-src/master/src/Parallel/Process.php
 */
final class ParallelProcess
{
    public Process $process;

    private WritableStreamInterface $in;

    /**
     * @var resource
     */
    private $stdOut;

    /**
     * @var resource
     */
    private $stdErr;

    /**
     * @var callable(mixed[]) : void
     */
    private $onData;

    /**
     * @var callable(\Throwable) : void
     */
    private $onError;

    private ?TimerInterface $timer = null;

    public function __construct(
        private string $command,
        private LoopInterface $loop,
    ) {
    }

    /**
     * @param callable(mixed[] $onData) : void $onData
     * @param callable(\Throwable $onError) : void $onError
     * @param callable(?int $onExit, string $output) : void $onExit
     */
    public function start(callable $onData, callable $onError, callable $onExit): void
    {
        // todo should I unlink this file after?
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
            $this->cancelTimer();

            rewind($this->stdErr);
            $onExit($exitCode, stream_get_contents($this->stdErr));
            fclose($this->stdErr);
        });
    }

    /**
     * @param mixed[] $data
     */
    public function request(array $data): void
    {
        $this->cancelTimer();
        $this->in->write($data);
        $this->timer = $this->loop->addTimer(60.0, function (): void {
            $onError = $this->onError;
            $onError(new \Exception('Child process timed out after 60 seconds',));
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

        $this->in->end();

        $this->process->terminate();
    }

    public function bindConnection(ReadableStreamInterface $out, WritableStreamInterface $in): void
    {
        $out->on(ReactEvent::DATA, function (array $json): void {
            $this->cancelTimer();
            if ($json[ReactCommand::ACTION] !== Action::RESULT) {
                return;
            }

            $onData = $this->onData;
            $onData($json['result']);
        });
        $this->in = $in;

        $out->on(ReactEvent::ERROR, function (\Throwable $error): void {
            $onError = $this->onError;
            $onError($error);
        });

        $in->on(ReactEvent::ERROR, function (\Throwable $error): void {
            $onError = $this->onError;
            $onError($error);
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
