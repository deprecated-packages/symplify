<?php

function overriddenSymbolFrom()
{
    if (true) { // + 1
        return true ? 'hey' : 'hou'; // + 2 (+ 1 for nesting)
    }
}

// Cognitive Complexity 3