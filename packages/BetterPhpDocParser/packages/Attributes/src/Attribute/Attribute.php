<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\Attributes\Attribute;

final class Attribute
{
    /**
     * Fully-qualified name
     *
     * @var string
     */
    public const FQN_NAME = 'fqn_name';

    /**
     * @var string
     */
    public const TYPE_AS_STRING = 'type_as_string';

    /**
     * @var string
     */
    public const START_TOKEN_POSITION = 'start_token_position';

    /**
     * @var string
     */
    public const END_TOKEN_POSITION = 'end_token_position';

    /**
     * @var string
     */
    public const TYPE_AS_ARRAY = 'type_as_array';
}
