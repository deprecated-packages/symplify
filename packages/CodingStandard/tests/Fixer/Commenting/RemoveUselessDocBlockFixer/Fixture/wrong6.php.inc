<?php

class SomeClass
{
    /**
     * @param ContainerBuilder $container A ContainerBuilder instance
     * @param AnotherInterface $another An AnotherInterface instance
     * @param OutputInterface $output      The output
     * @param OutputInterface $outputTwo      An Output instance with extra comment
     */
    protected function registerMappingDrivers(
        ContainerBuilder $container,
        AnotherInterface $another,
        OutputInterface $output,
        OutputInterface $outputTwo
    ): void {
    }
}

?>
-----
<?php

class SomeClass
{
    /**
     * @param OutputInterface $outputTwo      An Output instance with extra comment
     */
    protected function registerMappingDrivers(
        ContainerBuilder $container,
        AnotherInterface $another,
        OutputInterface $output,
        OutputInterface $outputTwo
    ): void {
    }
}

?>
