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
            // SomeTypeInterface => TypeInterface
            $type = substr($type, 0, -strlen('Interface'));
        }

        if (Strings::endsWith($type, '[]')) {
            return true;
        }

        $isDummyDescription = (bool) Strings::match(
                $description,
                sprintf('#^(A|An|The|the) (\\\\)?%s(Interface)?( instance)?$#i', preg_quote((string) $type, '/'))
            ) || ((strlen($description) < (strlen($type) + 10)) && levenshtein($type, $description) < 2);

        // improve with additional cases, probably regex
        if ($type && $isDummyDescription) {
            return false;
        }

        if ((strlen($description) < (strlen($type) + 10)) && levenshtein($name, $description) < 2) {
            return false;
        }

        return true;
    }
}
