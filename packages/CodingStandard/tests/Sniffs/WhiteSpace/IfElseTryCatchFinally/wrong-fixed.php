<?php declare(strict_types=1);
if ($count === 2) {
    return 3;

} elseif ($count === 3) {
    return 4;
}



try {
    return 1;

} catch (Exception $e) {
    return 2;

} finally {
    $this->someFunction();
}
