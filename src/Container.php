<?php

namespace DI;

use DI\Exceptions\NotFoundException;
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
        $this->resolver = new Resolver($this, $definitions);

        foreach ($definitions as $key => $definition) {
            $this->dependencies[$key] = $this->resolver->resolve($key);
        }
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        if (array_key_exists($id, $this->dependencies)) {
            return $this->dependencies[$id];
        } else {
            $dependency = $this->resolver->resolve($id);

            if (isset($dependency)) {
                return $dependency;
            } else {
                throw new NotFoundException();
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function has($id)
    {
        if (array_key_exists($id, $this->dependencies)) {
            return true;
        } else {
            return $this->resolver->isResolvable($id);
        }
    }
}