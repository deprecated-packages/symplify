<?php

declare(strict_types=1);

namespace Symplify\StaticDetector\ValueObject;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;

final class StaticClassMethodWithStaticCalls
{
    /**
     * @var StaticCall[]
     */
    private array $staticCalls = [];

    /**
     * @var string[]
     */
    private $staticCallsFilePathsWithLines = [];

    /**
     * @param StaticCall[] $staticCalls
     */
    public function __construct(
        private StaticClassMethod $staticClassMethod,
        array $staticCalls
    ) {
        $this->staticCalls = $staticCalls;
        $this->staticCallsFilePathsWithLines = $this->createFilePathsWithLinesFromNodes($staticCalls);
    }

    public function getStaticClassMethodName(): string
    {
        return $this->staticClassMethod->getClass() . '::' . $this->staticClassMethod->getMethod();
    }

    /**
     * @return StaticCall[]
     */
    public function getStaticCalls(): array
    {
        return $this->staticCalls;
    }

    public function getStaticCallFileLocationWithLine(): string
    {
        return $this->staticClassMethod->getFileLocationWithLine();
    }

    /**
     * @return string[]
     */
    public function getStaticCallsFilePathsWithLines(): array
    {
        return $this->staticCallsFilePathsWithLines;
    }

    public function getStaticCallsCount(): int
    {
        return count($this->staticCallsFilePathsWithLines);
    }

    /**
     * @param Node[] $nodes
     * @return string[]
     */
    private function createFilePathsWithLinesFromNodes(array $nodes): array
    {
        $filePathsWithLines = [];
        foreach ($nodes as $node) {
            $filePathsWithLines[] = $node->getAttribute(StaticDetectorAttributeKey::FILE_LINE);
        }

        return $filePathsWithLines;
    }
}
