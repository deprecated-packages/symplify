<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\Tag;

use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Context as TypeContext;
use Throwable;
use Webmozart\Assert\Assert;

/**
 * Same as @see \phpDocumentor\Reflection\DocBlock\Tags\Var,
 * just more tolerant to input. Allows invalid type.
 */
final class TolerantVar extends Var_
{
    /**
     * {@inheritdoc}
     */
    public static function create(
        $body,
        ?TypeResolver $typeResolver = null,
        ?DescriptionFactory $descriptionFactory = null,
        ?TypeContext $typeContext = null
    ) {
        Assert::stringNotEmpty($body);
        Assert::allNotNull([$typeResolver, $descriptionFactory]);

        $parts = preg_split('/(\s+)/Su', $body, 3, PREG_SPLIT_DELIM_CAPTURE);
        $type = null;
        $variableName = '';

        // if the first item that is encountered is not a variable; it is a type
        if (isset($parts[0]) && (strlen($parts[0]) > 0) && ($parts[0][0] !== '$')) {
            try {
                $type = $typeResolver->resolve(array_shift($parts), $typeContext);
            } catch (Throwable $throwable) {
                $type = null;
            }

            array_shift($parts);
        }

        // if the next item starts with a $ or ...$ it must be the variable name
        if (isset($parts[0]) && (strlen($parts[0]) > 0) && ($parts[0][0] === '$')) {
            $variableName = array_shift($parts);
            array_shift($parts);

            if (substr($variableName, 0, 1) === '$') {
                $variableName = substr($variableName, 1);
            }
        }

        $description = $descriptionFactory->create(implode('', $parts), $typeContext);

        return new static($variableName, $type, $description);
    }
}
