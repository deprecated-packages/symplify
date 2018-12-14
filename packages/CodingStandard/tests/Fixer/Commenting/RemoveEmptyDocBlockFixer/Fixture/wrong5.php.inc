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

function mustRun(OutputInterface $output)
{
}

?>
