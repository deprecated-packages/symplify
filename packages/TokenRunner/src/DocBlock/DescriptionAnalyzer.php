<?php declare(strict_types=1);

namespace Symplify\TokenRunner\DocBlock;

use Nette\Utils\Strings;

final class DescriptionAnalyzer
{
    public function isDescriptionUseful(string $description, ?string $type, ?string $name): bool
    {
        if (! $description || $type === null) {
            return false;
        }

        if (Strings::endsWith($type, 'Interface')) {
            // SomeTypeInterface => SomeType
            $type = substr($type, 0, -strlen('Interface'));
        }

        if (Strings::endsWith($type, '[]')) {
            return true;
        }

        $uselessPattern = sprintf(
            '#^((A|An|The|the) )?(\\\\)?%s(Interface)?( instance)?$#i',
            preg_quote((string) $type, '/')
        );

        $isDummyDescription = (bool) Strings::match($description, $uselessPattern ) ||
            ((strlen($description) < (strlen($type) + 10)) && levenshtein($type, $description) < 3);

        if ($type && $isDummyDescription) {
            return false;
        }

        if ((strlen($description) < (strlen($type) + 10)) && levenshtein($name, $description) < 3) {
            return false;
        }

        return true;
    }
}
