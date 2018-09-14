<?php declare(strict_types=1);

namespace Symplify\TokenRunner\DocBlock;

use Nette\Utils\Strings;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use Symplify\BetterPhpDocParser\PhpDocParser\TypeNodeAnalyzer;
use Symplify\BetterPhpDocParser\PhpDocParser\TypeNodeToStringsConvertor;
use function Safe\sprintf;
use function Safe\substr;

final class DescriptionAnalyzer
{
    /**
     * @var string
     */
    private const COMMENTED_PATTERN = '#^((A|An|The|the)( )?)?(\\\\)?%s(Interface)?( instance)?$#i';

    /**
     * @var TypeNodeAnalyzer
     */
    private $typeNodeAnalyzer;

    /**
     * @var TypeNodeToStringsConvertor
     */
    private $typeNodeToStringsConvertor;

    public function __construct(
        TypeNodeAnalyzer $typeNodeAnalyzer,
        TypeNodeToStringsConvertor $typeNodeToStringsConvertor
    ) {
        $this->typeNodeAnalyzer = $typeNodeAnalyzer;
        $this->typeNodeToStringsConvertor = $typeNodeToStringsConvertor;
    }

    public function isDescriptionUseful(string $description, ?TypeNode $typeNode, ?string $name): bool
    {
        if (! $description || $typeNode === null) {
            return false;
        }

        // array type, is is useful
        if ($this->typeNodeAnalyzer->containsArrayType($typeNode)) {
            return true;
        }

        $types = $this->typeNodeToStringsConvertor->convert($typeNode);

        // only 1 type can be analyzed
        $type = array_pop($types);

        if (Strings::endsWith($type, 'Interface')) {
            // SomeTypeInterface => SomeType
            $type = substr($type, 0, -strlen('Interface'));
        }

        $typeUselessPattern = sprintf(self::COMMENTED_PATTERN, preg_quote((string) $type, '/'));

        if ($type && $this->isDummyDescription($description, $typeUselessPattern, $type)) {
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

    /**
     * Just copy-pasting type(interface) or property name
     */
    private function isDummyDescription(string $description, string $typeUselessPattern, string $type): bool
    {
        if (Strings::match($description, $typeUselessPattern)) {
            return true;
        }

        return (strlen($description) < (strlen($type) + 10)) && levenshtein($type, $description) < 3;
    }
}
