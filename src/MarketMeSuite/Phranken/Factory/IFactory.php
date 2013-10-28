<?php
namespace MarketMeSuite\Phranken\Factory;

/**
 * defines methods that should exist for all factories
 */
interface IFactory
{
    /**
     * builds the target object with the provided arguments
     * @param  string $class The fully-qualified class name of the object to create
     * @param  array  $args  Any arguments to pass to the constructor of the object
     * @return mixed         The constructed target object
     */
    public function build($class, array $args = array());
}
