<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Console\Command;

use Nette\Utils\Strings;

final class CommandNaming
{
    /**
     * Converts:
     *  "SomeClass\SomeSuperCommand" → "some-super"
     *  "SomeClass\SOMESuperCommand" → "some-super"
     */
    public static function classToName(string $class): string
    {
        $shortClassName = self::getShortClassName($class);
        $rawCommandName = Strings::substring($shortClassName, 0, -strlen('Command'));

        // ECSCommand => ecs
        for ($i = 0; $i < strlen($rawCommandName); ++$i) {
            if (ctype_upper($rawCommandName[$i]) && self::isFollowedByUpperCaseLetterOrNothing($rawCommandName, $i)) {
                $rawCommandName[$i] = strtolower($rawCommandName[$i]);
            } else {
                break;
            }
        }

        $rawCommandName = lcfirst($rawCommandName);

        return Strings::replace($rawCommandName, '#[A-Z]#', function (array $matches): string {
            return '-' . strtolower($matches[0]);
        });
    }

    private static function getShortClassName(string $class): string
    {
        $classParts = explode('\\', $class);

        return array_pop($classParts);
    }

    private static function isFollowedByUpperCaseLetterOrNothing(string $string, int $position): bool
    {
        // this is the last letter
        if (! isset($string[$position + 1])) {
            return true;
        }

        // next letter is uppercase
        return ctype_upper($string[$position + 1]);
    }
}
