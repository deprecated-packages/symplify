<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

use Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;

/**
 * @deprecated Use directly passed array instead of juggling of ErrorAndDiffCollector
 */
final class ErrorAndDiffResultFactory
{
    public function __construct(
        private ErrorAndDiffCollector $errorAndDiffCollector
    ) {
    }

    /**
     * @deprecated Pass parameters to ErrorAndDiffResult() object directly, not via service juglging
     */
    public function create(): ErrorAndDiffResult
    {
        return new ErrorAndDiffResult(
            $this->errorAndDiffCollector->getErrors(),
            $this->errorAndDiffCollector->getFileDiffs(),
            $this->errorAndDiffCollector->getSystemErrors()
        );
    }
}
