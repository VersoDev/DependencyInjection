<?php

namespace DI;

class ContainerBuilder
{
    /**
     * @var array
     */
    private array $dependencies = [];

    /**
     * @param mixed $key
     * @param mixed $value
     *
     * @return self
     */
    public function addDependency($key, $value): self
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