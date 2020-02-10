<?php

namespace DI\Definitions;

class FactoryDefinition extends ClassDefinition
{
    /**
     * @var object
     */
    private object $instance;

    /**
     * FactoryDefinition constructor.
     * @param string $class
     * @param array $params
     */
    public function __construct(string $class, array $params)
    {
        parent::__construct($class, $params);
    }

    /**
     * @inheritDoc
     */
    public function resolve(): object
    {
        if (!isset($this->instance)) {
            $this->instance = parent::resolve()->build();
        }

        return $this->instance;
    }
}