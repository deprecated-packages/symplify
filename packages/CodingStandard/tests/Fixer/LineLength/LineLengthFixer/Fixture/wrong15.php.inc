<?php

class SomeClass
{
    public function someFunction()
    {
        $result = ['loooooooooooooooooooooooooooooooongArraaaaaaaaaaay', 'loooooooooooooooooooooongArraaaaaaaaaaaaaaay'];
    }
}

?>
-----
<?php

class SomeClass
{
    public function someFunction()
    {
        $result = [
            'loooooooooooooooooooooooooooooooongArraaaaaaaaaaay',
            'loooooooooooooooooooooongArraaaaaaaaaaaaaaay'
        ];
    }
}

?>
