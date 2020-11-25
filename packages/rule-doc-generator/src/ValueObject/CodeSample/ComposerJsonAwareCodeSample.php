<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\ValueObject\CodeSample;

use Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample;

final class ComposerJsonAwareCodeSample extends AbstractCodeSample
{
    /**
     * @var string
     */
    private $composerJson;

    public function __construct(string $goodCode, string $badCode, string $composerJson)
    {
        parent::__construct($goodCode, $badCode);

        $this->composerJson = $composerJson;
    }

    public function getComposerJson(): string
    {
        return $this->composerJson;
    }
}
