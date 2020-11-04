<?php

declare(strict_types=1);

namespace Symplify\Skipper\ValueObjectFactory;

use Nette\Utils\Strings;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\Skipper\ValueObject\Option;
use Symplify\Skipper\ValueObject\SkipRules;

final class SkipRulesFactory
{
    /**
     * @var string[]
     */
    private $skippedClasses = [];

    /**
     * @var string[]
     */
    private $skippedCodes = [];

    /**
     * @var array<string, string[]|null>
     */
    private $skippedMessages = [];

    /**
     * @var ParameterProvider
     */
    private $parameterProvider;

    public function __construct(ParameterProvider $parameterProvider)
    {
        $this->parameterProvider = $parameterProvider;
    }

    public function create(): SkipRules
    {
        $skip = $this->parameterProvider->provideArrayParameter(Option::SKIP);

        $this->skippedClasses = [];
        $this->skippedCodes = [];
        $this->skippedMessages = [];

        foreach ($skip as $key => $value) {
            if (is_int($key)) {
                $this->separateSkipItem($value, null);
                continue;
            }

            $this->separateSkipItem($key, $value);
        }

        return new SkipRules($this->skippedClasses, $this->skippedCodes, $this->skippedMessages);
    }

    private function separateSkipItem($key, $value): void
    {
        if (class_exists($key)) {
            $this->skippedClasses[$key] = $value;
            return;
        }

        $class = (string) Strings::before($key, '.');

        if (class_exists($class)) {
            $this->skippedCodes[$key] = $value;
            return;
        }

        $this->skippedMessages[$key] = $value;
    }
}
