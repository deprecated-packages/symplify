<?php

/**
 * Word-wrapping will be used so we don't break existing words.
 * Same paragraph.
 *
 * Another paragraph. Word-wrapping will be used so we don't break existing words.
 * Same paragraph
 */
function run()
{
}

?>
-----
<?php

/**
 * Word-wrapping will be used so we
 * don't break existing words. Same
 * paragraph.
 *
 * Another paragraph. Word-wrapping will
 * be used so we don't break existing
 * words. Same paragraph
 */
function run()
{
}

?>
