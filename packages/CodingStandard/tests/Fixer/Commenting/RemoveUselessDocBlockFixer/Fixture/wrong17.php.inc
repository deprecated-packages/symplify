<?php

namespace SomeNamespace;

class SomeClass
{
    /**
     * @param OutputInterface $output   An OutputInterface instance
     * @param string|Process  $cmd      An instance of Process or a command to run
     * @param string|null     $error    An error message that must be displayed if something went wrong
     */
    public function mustRun(OutputInterface $output, $cmd, $error = null, callable $callback = null)
    {
    }
}

?>
-----
<?php

namespace SomeNamespace;

class SomeClass
{
    /**
     * @param string|Process  $cmd      An instance of Process or a command to run
     * @param string|null     $error    An error message that must be displayed if something went wrong
     */
    public function mustRun(OutputInterface $output, $cmd, $error = null, callable $callback = null)
    {
    }
}

?>
