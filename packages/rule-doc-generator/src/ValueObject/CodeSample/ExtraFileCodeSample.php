<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\ValueObject\CodeSample;

use Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample;

final class ExtraFileCodeSample extends AbstractCodeSample
{
    /**
     * @var string
     */
    private $extraFile;

    public function __construct(string $goodCode, string $badCode, string $extraFile)
    {
        parent::__construct($goodCode, $badCode);

        $this->extraFile = $extraFile;
    }

    public function getExtraFile(): string
    {
        return $this->extraFile;
    }
}
