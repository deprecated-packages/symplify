<?php declare(strict_types=1);

namespace Symplify\TokenRunner\DocBlock;

use Nette\Utils\Strings;

final class DescriptionAnalyzer
{
    /**
     * @var string
     */
    private const COMMENTED_PATTERN = '#^((A|An|The|the)( )?)?(\\\\)?%s(Interface)?( instance)?$#i';

    public function isDescriptionUseful(string $description, ?string $type, ?string $name): bool
    {
        if (! $description || $type === null) {
            return false;
        }

        if (Strings::endsWith($type, 'Interface')) {
            // SomeTypeInterface => SomeType
            $type = substr($type, 0, -strlen('Interface'));
        }

        // array type, is is useful
        if (Strings::endsWith($type, '[]')) {
            return true;
        }

        $nameUselessPattern = sprintf(self::COMMENTED_PATTERN, preg_quote((string) $type, '/'));

        // just copy-pasting type(interface) or property name
        $isDummyDescription = (bool) Strings::match($description, $nameUselessPattern) ||
            ((strlen($description) < (strlen($type) + 10)) && levenshtein($type, $description) < 3);

        if ($type && $isDummyDescription) {
            return false;
        }

        // e.g. description: "The object manager" => "Theobjectmanager"
        $descriptionWithoutSpaces = str_replace(' ', '', $description);
        // e.g. name "$objectManagerName"
        $nameUselessPattern = sprintf(self::COMMENTED_PATTERN, preg_quote((string) $name, '#'));
        if ((bool) Strings::match($descriptionWithoutSpaces, $nameUselessPattern)) {
            return false;
        }

        // e.g. description: "The URL Generator"
        // e.g. type "UrlGenerator"
        $typeUselessPattern = sprintf(self::COMMENTED_PATTERN, preg_quote((string) $type, '#'));
        if ((bool) Strings::match($descriptionWithoutSpaces, $typeUselessPattern)) {
            return false;
        }

        // e.g. description: "The twig environment"
        // e.g. name + type  "twig" . "Environment"
        $typeAndNameUselessPattern = sprintf(self::COMMENTED_PATTERN, preg_quote((string) $name . $type, '#'));
        if ((bool) Strings::match($descriptionWithoutSpaces, $typeAndNameUselessPattern)) {
            return false;
        }

        return true;
    }
}
