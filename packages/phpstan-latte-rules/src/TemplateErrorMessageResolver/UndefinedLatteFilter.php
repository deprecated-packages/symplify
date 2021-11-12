<?php

declare(strict_types=1);

namespace Symplify\PHPStanLatteRules\TemplateErrorMessageResolver;

use Nette\Utils\Strings;
use Symplify\TemplatePHPStanCompiler\Contract\TemplateErrorMessageResolverInterface;
use Symplify\TemplatePHPStanCompiler\ValueObject\ErrorMessageWithTip;

final class UndefinedLatteFilter implements TemplateErrorMessageResolverInterface
{
    /**
     * @see https://regex101.com/r/vqPGnD/1
     * @var string
     */
    private const UNDEFINED_FILTER_REGEX = '/Access to an undefined property Latte\\\\Runtime\\\\FilterExecutor::\\$(?<undefined_filter>.*?)\\./';

    public function resolve(string $message): ?ErrorMessageWithTip
    {
        $match = Strings::match($message, self::UNDEFINED_FILTER_REGEX);
        if ($match) {
            $message = 'Undefined latte filter "' . $match['undefined_filter'] . '".';
            $tip = 'Register it in parameters > latteFilters. See https://github.com/symplify/symplify/tree/main/packages/phpstan-latte-rules#configuration';
            return new ErrorMessageWithTip($message, $tip);
        }
        return null;
    }
}
