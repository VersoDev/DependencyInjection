<?php

namespace DI;

use DI\Exceptions\InvalidDependencyException;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    /**
     * @var array
     */
    private array $dependencies = [];

    /**
     * @var Resolver
     */
    private Resolver $resolver;

    /**
     * Container constructor.
     * @param array $definitions
     */
    public function __construct(array $definitions)
    {
        $this->resolver = new Resolver($definitions);

        foreach (array_keys($definitions) as $definition) {
            $this->dependencies[$definition] = $this->resolver->resolveDefinition($definition);
        }
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        if ($this->has($id)) {
            return $this->dependencies[$id];
        } else {
            try {
                return $this->resolver->resolveDependency($id);
            } catch (InvalidDependencyException $e) {
                echo $e->getMessage();
            }
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function has($id)
    {
        return isset($this->dependencies[$id]);
    }
}