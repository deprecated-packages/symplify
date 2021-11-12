<?php

declare(strict_types=1);

namespace Symplify\TemplatePHPStanCompiler\Contract;

use Symplify\TemplatePHPStanCompiler\ValueObject\ErrorMessageWithTip;

interface TemplateErrorMessageResolverInterface
{
    public function resolve(string $message): ?ErrorMessageWithTip;
}
