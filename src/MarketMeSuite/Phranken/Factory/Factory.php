<?php
namespace MarketMeSuite\Phranken\Factory;

use \Reflector;
use \ReflectionClass;

/**
 * defines a base Factory class that contains helper methods
 * for building objects.
 *
 * @author Bill Nunney <bill@marketmesuite.com>
 */
abstract class Factory implements IFactory
{

    /**
     * builds the target object with the provided arguments
     * @param  string $class The fully-qualified class name of the object to create
     * @param  array  $args  Any arguments to pass to the constructor of the object
     * @return mixed         The constructed target object
     */
    public function build($class, array $args = array())
    {
        $refl = $this->getReflectionInstance($class);
        return $this->getInstance($refl, $args);
    }

    /**
     * Uses the reflection api to construct a new object.
     * @param  Reflector $refl A reflection class instance
     * @param  array     $args An array of arguments to pass to the object constructor,
     *                         when set to null no constructor arguemnts are passed
     * @return mixed           The constructed object
     */
    protected function getInstance(Reflector $refl, array $args = array())
    {
        // if no arguments were supplied, create an instance without
        // supplying constructor args
        if (empty($args)) {
            return $refl->newInstance();
        }

        // create an instance
        return $refl->newInstanceArgs($args);
    }
    
    /**
     * creates a new reflection instance with the loaded class
     * @param  string $class A fully qualified class name
     * @return Reflector     A Reflector loaded with $class
     */
    protected function getReflectionInstance($class)
    {
        return new ReflectionClass($class);
    }
    
    /**
     * asserts that the loaded Reflector's class has implemented a specific interface
     * @param  Reflector $refl      A Reflector loaded with the target class
     * @param  string    $interface A fully qualified interface name
     * @throws FactoryException If $refl does not implement $interface
     */
    protected function assertImplementsInterface(Reflector $refl, $interface)
    {
        if ($refl->implementsInterface($interface) === false) {
            throw new FactoryException('supplied class does not implement "' . $interface . '"');
        }
    }

    /**
     * asserts that the loaded Reflector's class directly inside the target namespace
     * @param  Reflector $refl                A Reflector loaded with the target class
     * @param  string    $targetNamespaceName A fully qualified namespace name
     * @throws FactoryException If $refl is not underneath $targetNamespaceName
     */
    protected function assertInNamespace(Reflector $refl, $targetNamespaceName)
    {
        $namespaceName = $refl->getNamespaceName();

        if ($namespaceName !== $targetNamespaceName) {
            throw new FactoryException('supplied class does not exist under namespace "' . $targetNamespaceName . '"');
        }
    }
}
