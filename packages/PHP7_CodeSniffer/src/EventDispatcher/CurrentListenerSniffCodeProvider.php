<?php declare(strict_types=1);

namespace Symplify\PHP7_CodeSniffer\EventDispatcher;

use ReflectionFunction;
use SplObjectStorage;
use Symplify\PHP7_CodeSniffer\Sniff\Naming\SniffNaming;

final class CurrentListenerSniffCodeProvider
{
    /**
     * @var array|callable
     */
    private $currentListener;

    /**
     * @var string[]
     */
    private $sniffClassToSniffCodeMap = [];

    public function __construct()
    {
        $this->sniffClassToSniffCodeMap = new SplObjectStorage();
    }

    /**
     * @param array|callable $currentListener
     */
    public function setCurrentListener($currentListener)
    {
        $this->currentListener = $currentListener;
    }

    public function getCurrentListenerSniffCode() : string
    {
        if (!is_callable($this->currentListener)) {
            return '';
        }

        if (isset($this->sniffClassToSniffCodeMap[$this->currentListener])) {
            return $this->sniffClassToSniffCodeMap[$this->currentListener];
        }

        $closureReflection = new ReflectionFunction($this->currentListener);
        if (!isset($closureReflection->getStaticVariables()['sniffObject'])) {
            return '';
        }

        $sniffClass = get_class($closureReflection->getStaticVariables()['sniffObject']);
        $sniffCode = SniffNaming::guessCodeByClass($sniffClass);

        return $this->sniffClassToSniffCodeMap[$this->currentListener] = $sniffCode;
    }
}
