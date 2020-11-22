<?php

declare(strict_types=1);

namespace Symplify\StaticDetector\ValueObject;

final class StaticReport
{
    /**
     * @var int
     */
    private $staticCallsCount;

    /**
     * @var StaticClassMethodWithStaticCalls[]
     */
    private $staticClassMethodsWithStaticCalls = [];

    /**
     * @param StaticClassMethodWithStaticCalls[] $staticClassMethodsWithStaticCalls
     */
    public function __construct(array $staticClassMethodsWithStaticCalls)
    {
        $staticCallsCount = 0;
        foreach ($staticClassMethodsWithStaticCalls as $staticClassMethodWithStaticCalls) {
            $staticCallsCount += count($staticClassMethodWithStaticCalls->getStaticCalls());
        }

        $this->staticCallsCount = $staticCallsCount;

        // sort from most called, to least called - the latter is easier to remove, so put low-hanging fruit first
        usort($staticClassMethodsWithStaticCalls, function (
            StaticClassMethodWithStaticCalls $firstStaticClassMethodWithStaticCalls,
            StaticClassMethodWithStaticCalls $secondStaticClassMethodWithStaticCalls
        ) {
            return $secondStaticClassMethodWithStaticCalls->getStaticCallsCount() <=> $firstStaticClassMethodWithStaticCalls->getStaticCallsCount();
        });

        $this->staticClassMethodsWithStaticCalls = $staticClassMethodsWithStaticCalls;
    }

    /**
     * @return StaticClassMethodWithStaticCalls[]
     */
    public function getStaticClassMethodsWithStaticCalls(): array
    {
        return $this->staticClassMethodsWithStaticCalls;
    }

    public function getStaticCallsCount(): int
    {
        return $this->staticCallsCount;
    }

    public function getStaticClassMethodCount(): int
    {
        return count($this->staticClassMethodsWithStaticCalls);
    }
}
