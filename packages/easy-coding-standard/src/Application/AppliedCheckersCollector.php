<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

final class AppliedCheckersCollector
{
    /**
     * @var array<class-string|string>
     */
    private array $appliedCheckerClasses = [];

    /**
     * @param class-string|string $checkerClass
     */
    public function addAppliedCheckerClass(string $checkerClass): void
    {
        $this->appliedCheckerClasses[] = $checkerClass;
    }

    /**
     * @return array<class-string|string>
     */
    public function getAppliedCheckerClasses(): array
    {
        return $this->appliedCheckerClasses;
    }

    public function resetAppliedCheckerClasses(): void
    {
        $this->appliedCheckerClasses = [];
    }
}
