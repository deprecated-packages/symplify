<?php

class SomeClass
{
    /**
     *
     * @param \OutputInterface $outputTwo An Output instance with extra comment
     */
    protected function registerMappingDrivers(
        ContainerBuilder $container,
        AnotherInterface $another,
        OutputInterface $output,
        OutputInterface $outputTwo
    ): void {
    }
}
