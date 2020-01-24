<?php

class SomeClass
{
    public function someFunction()
    {

        /**
         * Lambda function to filter user providers
         *
         * @param UserProviderInterface $userProvider
         *
         * @return bool
         */
        $filter = static function (UserProviderInterface $userProvider): bool {
            return $userProvider instanceof ApiKeyUserProvider;
        };
    }
}
