<?php

declare(strict_types=1);

namespace Symplify\PHPMDDecomposer\ValueObject\Config;

final class PHPStanConfig extends AbstractConfig
{
    /**
     * @var mixed[]
     */
    private $includes = [];

    /**
     * @var mixed[]
     */
    private $parameters = [];

    /**
     * @var mixed[]
     */
    private $rules = [];

    /**
     * @var array
     */
    private $matchingParameters = [];

    /**
     * @param mixed[] $rules
     * @param mixed[] $parameters
     * @param mixed[] $includes
     */
    public function __construct(
        array $rules = [],
        array $parameters = [],
        array $includes = [],
        array $matchingParameters = []
    ) {
        $this->includes = $includes;
        $this->parameters = $parameters;
        $this->rules = $rules;

        parent::__construct();

        $this->matchingParameters = $matchingParameters;
    }

    public function merge(self $phpStanConfig): void
    {
        $this->parameters = $this->mergeUnique($this->parameters, $phpStanConfig->getParameters());
        $this->includes = $this->mergeUnique($this->includes, $phpStanConfig->getIncludes());
        $this->rules = $this->mergeUnique($this->rules, $phpStanConfig->getRules());
    }

    /**
     * @return mixed[]
     */
    public function getIncludes(): array
    {
        return $this->includes;
    }

    /**
     * @return mixed[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @return mixed[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    public function isEmpty(): bool
    {
        if ($this->includes !== []) {
            return false;
        }

        if ($this->parameters !== []) {
            return false;
        }

        return $this->rules === [];
    }

    public function getMatchingParameters(): array
    {
        return $this->matchingParameters;
    }
}
