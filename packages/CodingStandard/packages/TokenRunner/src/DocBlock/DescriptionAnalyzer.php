<?php declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\DocBlock;

use Nette\Utils\Strings;
use PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\Type\AttributeAwareIdentifierTypeNode;
use Symplify\BetterPhpDocParser\Attributes\Attribute\Attribute;
use Symplify\BetterPhpDocParser\PhpDocParser\TypeNodeAnalyzer;

final class DescriptionAnalyzer
{
    /**
     * @var string
     */
    private const COMMENTED_PATTERN = '#^((A|An|The|the|current)(\s+)?)?(\\\\)?%s(Interface)?(\s+instance|itself)?$#i';

    /**
     * @var TypeNodeAnalyzer
     */
    private $typeNodeAnalyzer;

    public function __construct(TypeNodeAnalyzer $typeNodeAnalyzer)
    {
        sleep(3);

        $message = sprintf(
            '%s" is deprecated.%sUse "%s" directly instead.',
            self::class,
            PHP_EOL,
            NoSuperfluousPhpdocTagsFixer::class
        );
        trigger_error($message, E_USER_DEPRECATED);

        $this->typeNodeAnalyzer = $typeNodeAnalyzer;
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

        /** @var AttributeAwareIdentifierTypeNode|TypeNode $typeNode */
        $types = $typeNode->getAttribute(Attribute::TYPE_AS_ARRAY) ?: [];

        // only 1 type can be analyzed
        $type = array_pop($types);

        $type = $this->normalizeType($type);

        $typeUselessPattern = sprintf(self::COMMENTED_PATTERN, preg_quote($type, '#'));
        if ($type && $this->isDummyDescription($description, $typeUselessPattern, $type)) {
            return false;
        }

        // e.g. description: "The object manager" => "Theobjectmanager"
        $descriptionWithoutSpaces = str_replace(' ', '', $description);
        if (Strings::compare($name, $descriptionWithoutSpaces)) {
            return false;
        }

        // e.g. name "$objectManagerName"
        $nameUselessPattern = sprintf(self::COMMENTED_PATTERN, preg_quote((string) $name, '#'));
        if ((bool) Strings::match($descriptionWithoutSpaces, $nameUselessPattern)) {
            return false;
        }

        // e.g. description: "The URL Generator"
        // e.g. type "UrlGenerator"
        $typeUselessPattern = sprintf(self::COMMENTED_PATTERN, preg_quote($type, '#'));
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

    private function normalizeType(string $type): string
    {
        if (Strings::endsWith($type, 'Interface')) {
            // SomeTypeInterface => SomeType
            return Strings::substring($type, 0, -strlen('Interface'));
        }

        return $type;
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
