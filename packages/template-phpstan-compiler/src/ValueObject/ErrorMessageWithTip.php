<?php

declare(strict_types=1);

namespace Symplify\TemplatePHPStanCompiler\ValueObject;

final class ErrorMessageWithTip
{
    public function __construct(
        private string $errorMessage,
        private string $tip
    ) {
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function getTip(): string
    {
        return $this->tip;
    }
}
