<?php

namespace DI;

use DI\Types\Definition;

class ContainerBuilder
{
    /**
     * @var Definition[]
     */
    private array $dependencies;

    /**
     * ContainerBuilder constructor.
     */
    public function __construct()
    {
        $this->dependencies = [];
    }

    /**
     * @param mixed $key
     * @param mixed $value
     *
     * @return self
     */
    public function addDefinition($key, $value): self
    {
        $this->dependencies[$key] = $value;

        return $this;
    }

    /**
     * @return Container
     */
    public function build(): Container
    {
        return new Container($this->dependencies);
    }
}