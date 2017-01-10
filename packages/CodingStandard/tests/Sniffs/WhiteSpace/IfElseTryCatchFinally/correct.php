<?php

if ($i === 1) {
	return $i;

} else {
	return $i * 2;
}


try {
	return 1;

} catch (Exception $e) {
	return 2;

} finally {
	$this->someFunction();
}
