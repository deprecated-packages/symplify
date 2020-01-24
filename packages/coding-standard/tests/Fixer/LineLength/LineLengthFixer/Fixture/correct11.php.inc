<?php

declare(strict_types=1);

class ListDataProvider
{
    public function createListDataDTOForGroup(PlayerGroup $group): void
    {
        $group->prefixDefaultPaginatorOptions("group{$group->getId()}");
    }
}
