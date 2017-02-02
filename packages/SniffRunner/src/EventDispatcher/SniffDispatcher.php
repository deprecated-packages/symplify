<?php declare(strict_types=1);

namespace Symplify\SniffRunner\EventDispatcher;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symplify\SniffRunner\EventDispatcher\Event\CheckFileTokenEvent;

final class SniffDispatcher extends EventDispatcher
{
    /**
     * @param Sniff[] $sniffs
     */
    public function addSniffListeners(array $sniffs)
    {
        foreach ($sniffs as $sniff) {
            foreach ($sniff->register() as $token) {
                $this->addTokenSniffListener($token, $sniff);
            }
        }
    }

    /**
     * @param string|int $token
     * @param Sniff $sniffObject
     */
    private function addTokenSniffListener($token, Sniff $sniffObject)
    {
        // @todo, string or int? Make it strict!
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
