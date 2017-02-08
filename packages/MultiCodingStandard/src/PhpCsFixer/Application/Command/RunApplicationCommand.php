<?php declare(strict_types=1);

namespace Symplify\MultiCodingStandard\PhpCsFixer\Application\Command;

final class RunApplicationCommand
{
    /**
     * @var array
     */
    private $sources;

    /**
     * @var array
     */
    private $rules;

    /**
     * @var array
     */
    private $excludedRules;

    /**
     * @var bool
     */
    private $isFixer;

    public function __construct(array $sources, array $rules, array $excludeRules, bool $isFixer)
    {
        $this->sources = $sources;
        $this->rules = $rules;
        $this->excludedRules = $excludeRules;
        $this->isFixer = $isFixer;
    }

    public function getSources() : array
    {
        return $this->sources;
    }

    public function getRules() : array
    {
        return $this->rules;
    }

    public function getExcludedRules() : array
    {
        return $this->excludedRules;
    }

    public function isFixer() : bool
    {
        return $this->isFixer;
    }
}
