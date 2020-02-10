<?php

namespace DI;

use DI\Exceptions\InvalidDependencyException;
use DI\Types\ArrayDefinition;
use DI\Types\ClassDefinition;
use DI\Types\Definition;
use DI\Types\ScalarDefinition;
use ReflectionClass;
use ReflectionException;
use RuntimeException;

class Resolver
{
    /**
     * @var Definition[]
     */
    private array $definitions = [];

    /**
     * Resolver constructor.
     * @param array $definitions
     */
    public function __construct(array $definitions)
    {
        foreach ($definitions as $key => $definition) {
            $this->definitions[$key] = $this->parse($definition);
        }
    }

    /**
     * @param mixed $definition
     * @return Definition|null
     */
    private function parse($definition): ?Definition
    {
        // Gestion des clés (de la forme %cle%)
        if (is_string($definition)) {
            if (preg_match("#^%(.+)%$#", $definition, $match)) {
                if (isset($this->definitions[$match[1]])) {
                    return $this->definitions[$match[1]];
                } else {
                    //Erreur l'indice n'existe pas
                }
            } else {
                return new ScalarDefinition($definition);
            }
        } else if (is_scalar($definition)) {
            return new ScalarDefinition($definition);
        } else if (is_array($definition)) {
            return new ArrayDefinition($definition);
        } else {
            if (isset($this->definitions[$definition])) {
                return $this->definitions[$definition];
            } else {
                if (class_exists($definition) || interface_exists($definition)) {
                    try {
                        $class = new ReflectionClass($definition);

                        if ($class->isInstantiable()) {
                            $constructor = $class->getConstructor();

                            if (!is_null($constructor)) {
                                $parameters = [];

                                foreach ($constructor->getParameters() as $parameter) {
                                    $parameterType = $parameter->getType();

                                    if (!is_null($parameterType)) {
                                        if (class_exists($parameterType->getName()) || interface_exists($parameterType->getName())) {
                                            $parameters[] = $this->parse($parameterType->getName());
                                        } else {
                                            // TODO : change for annotations
                                            // Si le constructeur a une annot @Inject sur le paramètre en question, c'est ok
                                            if (isset($this->definitions[$parameter->getName()])) {
                                                $parameters[] = $this->definitions[$parameter->getName()];
                                            } else {
                                                throw new RuntimeException("Impossible to resolve the dependency");
                                            }
                                        }
                                    } else {
                                        throw new RuntimeException("The field has no declared types");
                                    }
                                }

                                return new ClassDefinition($class, $parameters);
                            } else {
                                return new ClassDefinition($class);
                            }
                        } else {
                            throw new RuntimeException("Non instanciable class");
                        }
                    } catch (ReflectionException $ignored) {
                        return null;
                    }
                }

            }
        }

        return null;
    }

    /**
     * @param mixed $definition
     * @return mixed
     */
    public function resolveDefinition($definition)
    {
        return $this->definitions[$definition]->resolve();
    }

    /**
     * @param $dependency
     * @return mixed
     * @throws InvalidDependencyException
     */
    public function resolveDependency($dependency)
    {
        if (isset($this->definitions[$dependency])) {
            return $this->definitions[$dependency]->resolve();
        } else {
            if (class_exists($dependency)) {
                $dependency = $this->parse($dependency);

                return $dependency->resolve();
            } else {
                throw new InvalidDependencyException();
            }
        }
    }
}