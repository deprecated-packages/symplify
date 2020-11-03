<?php

function overriddenSymbolFrom()
{
    if (true) { // operation: +1
        return true ? 'hey' : 'hou'; // operation: +1, nesting: +1
    }
}

-----
3
