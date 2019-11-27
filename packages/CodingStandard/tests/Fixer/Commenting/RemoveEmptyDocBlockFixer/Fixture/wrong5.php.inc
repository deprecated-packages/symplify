<?php

namespace SomeNamespace;

    /**
     * @package foo
     * @param OutputInterface $output
     */
function mustRun(OutputInterface $output)
{
}

?>
-----
<?php

namespace SomeNamespace;

/**
 * @param OutputInterface $output
 */
function mustRun(OutputInterface $output)
{
}

?>
