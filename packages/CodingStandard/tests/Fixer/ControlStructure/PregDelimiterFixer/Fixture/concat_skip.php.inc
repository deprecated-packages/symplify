<?php

use Nette\Utils\Strings;

Strings::replace($file, '#' . preg_quote(getcwd() . '/') . '#');
