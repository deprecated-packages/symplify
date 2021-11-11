<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\DibiMaskMatchesVariableTypeRule\Fixture;

use Dibi\Connection;

final class SkipArrayUnionArray
{
    public function __construct(
        private Connection $db
    ) {
    }

    public function run(array|null $userIds, array|null $ids)
    {
        $params = [];
        if ($userIds !== null) {
            $params = [
                'user_id%in' => $userIds,
            ];
        }
        if ($ids !== null) {
            $params = [
                'id%in' => $ids,
            ];
        }

        return $this->db->query('SELECT [id], [user_id] FROM [...] WHERE %and', $params);
    }
}
