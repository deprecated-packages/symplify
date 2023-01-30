<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\ValueObject\CodeSample;

use Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample;

final class ExtraFileCodeSample extends AbstractCodeSample
{
    public function __construct(
        string $badCode,
        string $goodCode,
        private readonly string $extraFile
    ) {
        parent::__construct($badCode, $goodCode);
    }

    public function getExtraFile(): string
    {
        return $this->extraFile;
    }
}
