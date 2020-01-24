<?php

$this->assertTrue(class_exists('\ApiGen\Reflection\Tests\Parser\ExtendingSources\ExtendingClass'));

?>
-----
<?php

$this->assertTrue(class_exists(\ApiGen\Reflection\Tests\Parser\ExtendingSources\ExtendingClass::class));

?>
