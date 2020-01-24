<?php

use Nette\Utils\Strings;

Strings::match($value, '/value/');

?>
-----
<?php

use Nette\Utils\Strings;

Strings::match($value, '#value#');

?>
