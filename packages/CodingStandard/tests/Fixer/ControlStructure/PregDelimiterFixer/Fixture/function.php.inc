<?php

preg_match('~value~', $value);
preg_match_all('~value~', $value);
preg_match_all("~value~", $value);
preg_match_all("~value~i", $value);
preg_match_all('~value~im', $value);
preg_match_all('', $value);

?>
-----
<?php

preg_match('#value#', $value);
preg_match_all('#value#', $value);
preg_match_all("#value#", $value);
preg_match_all("#value#i", $value);
preg_match_all('#value#im', $value);
preg_match_all('', $value);

?>
