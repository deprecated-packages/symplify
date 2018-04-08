<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\Tag;

use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use phpDocumentor\Reflection\DocBlock\Tags\BaseTag;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Context as TypeContext;
use Throwable;
use Webmozart\Assert\Assert;

/**
 * Same as @see \phpDocumentor\Reflection\DocBlock\Tags\Var,
 * just more tolerant to input. Allows invalid type.
 */
final class TolerantVar extends BaseTag
{
    /**
     * @var string
     */
    protected $name = 'var';

    /**
     * @var string
     * */
    protected $variableName = '';

    /**
     * @var string
     */
    protected $type;

    public function __construct(string $variableName, ?Type $type = null, ?Description $description = null)
    {
        Assert::string($variableName);

        $this->variableName = $variableName;
        $this->type = $type;
        $this->description = $description;
    }

    public function __toString(): string
    {
        return sprintf(
            '%s%s%s',
            $this->type ? $this->type . ' ' : '',
            $this->variableName ? '$' . $this->variableName : '',
            $this->description ? ' ' . $this->description : ''
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function create(
        $body,
        ?TypeResolver $typeResolver = null,
        ?DescriptionFactory $descriptionFactory = null,
        ?TypeContext $typeContext = null
    ) {
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

    public function getVariableName(): string
    {
        return $this->variableName;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }
}
