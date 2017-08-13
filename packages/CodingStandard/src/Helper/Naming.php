<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Helper;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
use Symplify\CodingStandard\Exception\UnexpectedTokenException;

final class Naming
{
    /**
     * @var string[]
     */
    private static $controllerNameSuffixes = ['Controller', 'Presenter'];

    public static function isControllerClass(File $file, int $position): bool
    {
        self::ensureIsClassToken($file, $position);

        $className = $file->getDeclarationName($position);
        if (! $className) {
            return false;
        }

        foreach (self::$controllerNameSuffixes as $controllerNameSuffix) {
            if (Strings::endsWith($className, $controllerNameSuffix)) {
                return true;
            }
        }

        return false;
    }

    private static function ensureIsClassToken(File $file, int $position): void
    {
        $token = $file->getTokens()[$position];
        if ($token['code'] === T_CLASS) {
            return;
        }

        throw new UnexpectedTokenException(sprintf(
            'This requires "%s" token. "%s" given.',
            'T_CLASS',
            $token['type']
        ));
    }
}
