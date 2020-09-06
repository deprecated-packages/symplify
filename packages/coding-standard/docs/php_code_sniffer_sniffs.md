# 1 PHP Code Sniffer Sniff

## There should not be comments with valid code

- class: [`CommentedOutCodeSniff`](../src/Sniffs/Debug/CommentedOutCodeSniff.php)

```php
<?php

// $file = new File;
// $directory = new Diretory([$file]);
```

:x:
