<?php

namespace SomeNamespace;

/**
 * @param OutputInterface         $missingType
 * @package OutputInterfaceLonger $output
 * @param OtherInterface          $anoterMissingType
 */
function mustRun(OutputInterface $output, $missingType, $anoterMissingType)
{
}

?>
-----
<?php

namespace SomeNamespace;

/**
 * @param OutputInterface $missingType
 * @param OtherInterface  $anoterMissingType
 */
function mustRun(OutputInterface $output, $missingType, $anoterMissingType)
{
}

?>
