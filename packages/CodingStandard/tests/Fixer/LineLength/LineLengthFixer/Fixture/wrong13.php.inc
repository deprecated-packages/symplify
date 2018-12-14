<?php

class SomeClass
{
    public function someFunction()
    {
        yield CreateRoleCommand::fromValueObjects(RoleId::fromString(self::REALTY_ECONOMY), 'Předpisář', ScopeId::fromString(ScopeList::REALTY), PrivilegeList::REALTY_PRIVILEGES);
    }
}

?>
-----
<?php

class SomeClass
{
    public function someFunction()
    {
        yield CreateRoleCommand::fromValueObjects(
            RoleId::fromString(self::REALTY_ECONOMY),
            'Předpisář',
            ScopeId::fromString(ScopeList::REALTY),
            PrivilegeList::REALTY_PRIVILEGES
        );
    }
}

?>
