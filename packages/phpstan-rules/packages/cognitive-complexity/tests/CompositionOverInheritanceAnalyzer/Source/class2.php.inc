<?php

trait T1 {}
trait T2 {}

final class A extends B { // +25 (extending another class)
    use T1, T2; // +10 for each trait
}

-----
45
