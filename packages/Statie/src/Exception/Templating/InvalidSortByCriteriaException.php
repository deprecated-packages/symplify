<?php declare(strict_types=1);

namespace Symplify\Statie\Exception\Templating;

use Exception;
use function Safe\sprintf;

final class InvalidSortByCriteriaException extends Exception
{
    /**
     * @param mixed[] $possibleValues
     */
    public function __construct(string $invalidValue, array $possibleValues)
    {
        $message = sprintf(
            'Invalid criteria "%s" passed to "%s". Pick one of: "%s"',
            $invalidValue,
            'sortByField',
            implode('", "', $possibleValues)
        );

        parent::__construct($message);
    }
}
