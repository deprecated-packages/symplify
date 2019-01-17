<?php declare(strict_types=1);

namespace Symplify\Statie\Exception\Templating;

use Exception;

final class InvalidSortByCriteriaException extends Exception
{
    public function __construct(string $invalidValue, array $possibleValues)
    {
        $message = \Safe\sprintf(
            'Invalid criteria "%s" passed to "%s". Pick one of: "%s"',
            $invalidValue,
            'sortByField',
            implode('", "', $possibleValues)
        );

        parent::__construct($message);
    }
}
