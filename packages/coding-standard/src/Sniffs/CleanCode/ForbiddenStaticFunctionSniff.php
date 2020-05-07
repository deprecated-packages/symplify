<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\CleanCode;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * @deprecated
 */
final class ForbiddenStaticFunctionSniff implements Sniff
{
    /**
     * @var string[]
     */
    private const ALLOWED_STATIC_FUNCTIONS = [
        'getSubscribedEvents', # Symfony of event subscriber
    ];

    public function __construct()
    {
        trigger_error(sprintf(
            'Sniff "%s" is deprecated and will be removed in Symplify 8 (May 2020). Use "%s" instead',
            self::class,
            'https://github.com/symplify/coding-standard/blob/master/src/Rules/Naming/NoClassWithStaticMethodWithoutStaticNameRule.php'
        ));

        sleep(3);
    }

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_STATIC];
    }

    /**
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $functionTokenPosition = $file->findNext([T_FUNCTION], $position, $position + 3);
        if ($functionTokenPosition === false) {
            return;
        }

        $functionNameTokenPosition = $file->findNext([T_STRING], $functionTokenPosition, $functionTokenPosition + 3);
        $functionNameToken = $file->getTokens()[$functionNameTokenPosition];

        if ($functionNameToken === false) {
            return;
        }

        if (in_array($functionNameToken['content'], self::ALLOWED_STATIC_FUNCTIONS, true)) {
            return;
        }

        $file->addError(
            sprintf('Use services and constructor injection over static method "%s"', $functionNameToken['content']),
            $position,
            self::class
        );
    }
}
