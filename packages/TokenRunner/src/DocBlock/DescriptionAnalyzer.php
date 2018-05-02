<?php declare(strict_types=1);

namespace Symplify\TokenRunner\DocBlock;

use Nette\Utils\Strings;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use Symplify\BetterPhpDocParser\PhpDocParser\TypeNodeAnalyzer;
use Symplify\BetterPhpDocParser\PhpDocParser\TypeNodeToStringsConvertor;

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

    public function __construct(TypeNodeAnalyzer $typeNodeAnalyzer, TypeNodeToStringsConvertor $typeNodeToStringsConvertor)
    {
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

        $isDescriptionUseful = true;

        $types = $this->typeNodeToStringsConvertor->convert($typeNode);

        foreach ($types as $type) {
            $isDescriptionUseful = true;

            if (Strings::endsWith($type, 'Interface')) {
                // SomeTypeInterface => SomeType
                $type = substr($type, 0, -strlen('Interface'));
            }

            $typeUselessPattern = sprintf(self::COMMENTED_PATTERN, preg_quote((string) $type, '/'));

            // just copy-pasting type(interface) or property name
            $isDummyDescription = (bool) Strings::match($description, $typeUselessPattern) ||
                ((strlen($description) < (strlen($type) + 10)) && levenshtein($type, $description) < 3);

            if ($type && $isDummyDescription) {
                $isDescriptionUseful = false;
                continue;
            }

            // e.g. description: "The object manager" => "Theobjectmanager"
            $descriptionWithoutSpaces = str_replace(' ', '', $description);

            // e.g. name "$objectManagerName"
            $nameUselessPattern = sprintf(self::COMMENTED_PATTERN, preg_quote((string) $name, '#'));
            if ((bool) Strings::match($descriptionWithoutSpaces, $nameUselessPattern)) {
                $isDescriptionUseful = false;
                continue;
            }

            // e.g. description: "The URL Generator"
            // e.g. type "UrlGenerator"
            $typeUselessPattern = sprintf(self::COMMENTED_PATTERN, preg_quote((string) $type, '#'));
            if ((bool) Strings::match($descriptionWithoutSpaces, $typeUselessPattern)) {
                $isDescriptionUseful = false;
                continue;
            }

            // e.g. description: "The twig environment"
            // e.g. name + type  "twig" . "Environment"
            $typeAndNameUselessPattern = sprintf(self::COMMENTED_PATTERN, preg_quote((string) $name . $type, '#'));
            if ((bool) Strings::match($descriptionWithoutSpaces, $typeAndNameUselessPattern)) {
                $isDescriptionUseful = false;
                continue;
            }
        }

        return $isDescriptionUseful;
    }
}
