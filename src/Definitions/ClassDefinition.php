<?php

namespace DI\Definitions;

class ClassDefinition implements Definition
{
    /**
     * @var string
     */
    private string $class;

    /**
     * @var Definition[]
     */
    private array $params;

    /**
     * @var object
     */
    private object $instance;

    /**
     * ClassDefinition constructor.
     * @param string $class
     * @param Definition[] $params
     */
    public function __construct(string $class, array $params = [])
    {
        $this->class = $class;
        $this->params = $params;
    }

    /**
     * @inheritDoc
     */
    public function resolve(): object
    {
        if (!isset($this->instance)) {
            $params = [];

            foreach ($this->params as $param) {
                $params[] = $param->resolve();
            }

            $class = $this->class;
            $this->instance = new $class(...$params);
        }

        return $this->instance;
    }
}