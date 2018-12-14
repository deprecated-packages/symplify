<?php

class SomeClass
{
    public function someFunction()
    {
        foreach ($finder as $fileInfo) {
            $processInput[] = sprintf('%s=%s', $fileInfo->getRelativePathname(), $newPackageDirectory . '/' . $fileInfo->getRelativePathname());
        }
    }
}

?>
-----
<?php

class SomeClass
{
    public function someFunction()
    {
        foreach ($finder as $fileInfo) {
            $processInput[] = sprintf(
                '%s=%s',
                $fileInfo->getRelativePathname(),
                $newPackageDirectory . '/' . $fileInfo->getRelativePathname()
            );
        }
    }
}

?>
