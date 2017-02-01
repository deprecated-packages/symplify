<?php declare(strict_types=1);

namespace Symplify\PHP7_CodeSniffer\EventDispatcher;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symplify\PHP7_CodeSniffer\EventDispatcher\Event\CheckFileTokenEvent;

final class SniffDispatcher extends EventDispatcher
{
    /**
     * @param Sniff[] $sniffs
     */
    public function addSniffListeners(array $sniffs)
    {
        foreach ($sniffs as $sniffCode => $sniffObject) {
            $tokens = $sniffObject->register();
            foreach ($tokens as $token) {
                $this->addTokenSniffListener($token, $sniffObject);
            }
        }
    }

    /**
     * @param string|int $token
     * @param Sniff $sniffObject
     */
    private function addTokenSniffListener($token, Sniff $sniffObject)
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
