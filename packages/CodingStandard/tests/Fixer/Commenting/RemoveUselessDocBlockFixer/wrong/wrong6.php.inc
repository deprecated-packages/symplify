<?php

class SomeClass
{
    /**
     * @param ContainerBuilder $container A ContainerBuilder instance
     * @param AnotherInterface $another An AnotherInterface instance
     * @param OutputInterface $output      An Output instance
     */
    protected function registerMappingDrivers(
        ContainerBuilder $container,
        AnotherInterface $another,
        OutputInterface $output
    ): void {
    }
}
