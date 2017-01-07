<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\EventDispatcher;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symplify\PHP7_CodeSniffer\EventDispatcher\Event\CheckFileTokenEvent;

final class SniffDispatcher extends EventDispatcher
{
    /**
     * @var CurrentListenerSniffCodeProvider
     */
    private $currentListenerSniffCodeProvider;

    public function __construct(CurrentListenerSniffCodeProvider $currentListenerSniffCodeProvider)
    {
        $this->currentListenerSniffCodeProvider = $currentListenerSniffCodeProvider;
    }

    /**
     * @param Sniff[] $sniffs
     */
    public function addSniffListeners(array $sniffs)
    {
        foreach ($sniffs as $sniffCode => $sniffObject) {
            $tokens = $sniffs[$sniffCode]->register();
            foreach ($tokens as $token) {
                $this->addTokenSniffListener($token, $sniffObject);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function doDispatch($listeners, $eventName, Event $event)
    {
        foreach ($listeners as $listener) {
            if ($event->isPropagationStopped()) {
                break;
            }

            $this->currentListenerSniffCodeProvider->setCurrentListener($listener);
            call_user_func($listener, $event, $eventName, $this);
        }
    }

    private function addTokenSniffListener(string $token, Sniff $sniffObject)
    {
        $this->addListener(
            $token,
            function (CheckFileTokenEvent $checkFileToken) use ($sniffObject) {
                $sniffObject->process(
                    $checkFileToken->getFile(),
                    $checkFileToken->getStackPointer()
                );
            }
        );
    }
}
