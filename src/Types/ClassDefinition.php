<?php

namespace DI\Types;

use ReflectionClass;

class ClassDefinition implements Definition
{
    /**
     * @var ReflectionClass
     */
    private ReflectionClass $class;

    /**
     * @var array
     */
    private array $params;

    /**
     * @var object
     */
    private object $instance;

    /**
     * ClassDefinition constructor.
     * @param ReflectionClass $class
     * @param array $params
     */
    public function __construct(ReflectionClass $class, array $params = [])
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

            $this->instance = $this->class->newInstance(...$params);
        }

        return $this->instance;
    }
}