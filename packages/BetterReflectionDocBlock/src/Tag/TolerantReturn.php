<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\Tag;

use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use phpDocumentor\Reflection\DocBlock\Tags\BaseTag;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Context;
use Throwable;
use Webmozart\Assert\Assert;

/**
 * Same as @see \phpDocumentor\Reflection\DocBlock\Tags\Return_,
 * just more tolerant to input. Allows no type in return tag, e.g.
 *
 * - "_return $value"
 */
final class TolerantReturn extends BaseTag
{
    /**
     * @var string
     */
    protected $name = 'return';

    /**
     * @var Type
     */
    private $type;

    public function __construct(?Type $type = null, ?Description $description = null)
    {
        $this->type = $type;
        $this->description = $description;
    }

    public function __toString(): string
    {
        return $this->type . ' ' . $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(
        string $body,
        ?TypeResolver $typeResolver = null,
        ?DescriptionFactory $descriptionFactory = null,
        ?Context $context = null
    ): self {
        Assert::string($body);
        Assert::allNotNull([$typeResolver, $descriptionFactory]);

        $parts = preg_split('/\s+/Su', $body, 2);

        // tolerant part here
        try {
            $type = $typeResolver->resolve($parts[0] ?? '', $context);
        } catch (Throwable $throwable) {
            $type = null;
        }

        $description = $descriptionFactory->create($parts[1] ?? '', $context);

        return new static($type, $description);
    }

    public function getType(): ?Type
    {
        return $this->type;
    }
}
