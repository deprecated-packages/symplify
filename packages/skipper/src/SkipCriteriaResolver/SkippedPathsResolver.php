<?php

declare(strict_types=1);

namespace Symplify\Skipper\SkipCriteriaResolver;

use Nette\Utils\Strings;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\Skipper\ValueObject\Option;

final class SkippedPathsResolver
{
    /**
     * @var ParameterProvider
     */
    private $parameterProvider;

    /**
     * @var string[]
     */
    private $skippedPaths = [];

    public function __construct(ParameterProvider $parameterProvider)
    {
        $this->parameterProvider = $parameterProvider;
    }

    /**
     * @return string[]
     */
    public function resolve(): array
    {
        $skip = $this->parameterProvider->provideArrayParameter(Option::SKIP);

        foreach ($skip as $key => $value) {
            if (! is_int($key)) {
                continue;
            }

            if (file_exists($value)) {
                $this->skippedPaths[] = $value;
                continue;
            }

            if (Strings::contains($value, '*')) {
                $this->skippedPaths[] = $value;
                continue;
            }
        }

        return $this->skippedPaths;
    }
}
