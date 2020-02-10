<?php

namespace DI;

use DI\Definitions\ArrayDefinition;
use DI\Definitions\ClassDefinition;
use DI\Definitions\Definition;
use DI\Definitions\FactoryDefinition;
use DI\Definitions\ScalarDefinition;
use DI\Definitions\StaticDefinition;
use DI\Exceptions\NonInstantiableDependencyException;
use DI\Exceptions\UndefinedDependencyKeyException;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;

/**
 * Class Resolver
 * @package DI
 */
class Resolver
{
    /**
     * @var Definition[] Array of resolved definitions.
     */
    private array $definitions = [];

    /**
     * Resolver constructor.
     *
     * @param ContainerInterface $container Container of the App.
     * @param array $definitions Definitions created in the config.
     */
    public function __construct(ContainerInterface $container, array $definitions)
    {
        // Add the ContainerInterface to the definitions as a StaticDefinition
        $this->definitions[ContainerInterface::class] = new StaticDefinition($container);

        foreach ($definitions as $key => $definition) {
            try {
                $this->definitions[$key] = $this->make($definition);
            } catch (NonInstantiableDependencyException $e) {
            } catch (UndefinedDependencyKeyException $e) {
            }
        }
    }

    /**
     * Resolve a dependency.
     *
     * @param mixed $dependency Dependency to resolve.
     * @return mixed|null Resolved dependency.
     */
    public function resolve($dependency)
    {
        if (array_key_exists($dependency, $this->definitions)) {
            return ($this->definitions[$dependency])->resolve();
        } else {
            try {
                $definition = $this->make($dependency);

                if (isset($definition)) {
                    return $definition->resolve();
                } else {
                    return null;
                }
            } catch (NonInstantiableDependencyException | UndefinedDependencyKeyException $ignored) {
                echo $ignored->getMessage();

                return null;
            }
        }
    }

    /**
     * Constructs a definition associated with $dependency.
     *
     * @param mixed $dependency Dependency to create.
     * @return Definition|null Created definition.
     * @throws NonInstantiableDependencyException
     * @throws UndefinedDependencyKeyException
     */
    private function make($dependency): ?Definition
    {
        if (is_string($dependency)) {
            if (preg_match("#^%(.+)%$#", $dependency, $key)) {
                if (isset($this->definitions[$key[1]])) {
                    return $this->definitions[$key[1]];
                } else {
                    throw new UndefinedDependencyKeyException();
                }
            } else if (class_exists($dependency) || interface_exists($dependency)) {
                if (array_key_exists($dependency, $this->definitions)) {
                    return $this->definitions[$dependency];
                } else {
                    try {
                        $class = new ReflectionClass($dependency);

                        if ($class->isInstantiable()) {
                            $constructor = $class->getConstructor();

                            if ($constructor) {
                                $params = [];

                                foreach ($constructor->getParameters() as $parameter) {
                                    if (($type = $parameter->getType()) && $type instanceof ReflectionNamedType) {
                                        $params[] = $this->make($type->getName());
                                    } else {
                                        // Erreur : le paramètre ne possède pas de type
                                        // Peut être réglé avec un ->constructor()
                                    }
                                }

                                if ($class->implementsInterface(FactoryInterface::class)) {
                                    return new FactoryDefinition($class->getName(), $params);
                                } else {
                                    return new ClassDefinition($class->getName(), $params);
                                }
                            } else {
                                return new ClassDefinition($class->getName());
                            }
                        } else {
                            throw new NonInstantiableDependencyException();
                        }

                    } catch (ReflectionException $ignored) {
                    }
                }
            }
        }

        if (is_scalar($dependency)) {
            return new ScalarDefinition($dependency);
        } else if (is_array($dependency)) {
            return new ArrayDefinition($dependency);
        }

        return null;
    }

    /**
     * Indicate if an id is resolvable.
     *
     * @param string $id Id to test.
     * @return bool Result of the test.
     */
    public function isResolvable(string $id): bool
    {
        try {
            $definition = $this->make($id);
        } catch (NonInstantiableDependencyException $e) {
        } catch (UndefinedDependencyKeyException $e) {
        }

        return isset($definition);
    }
}