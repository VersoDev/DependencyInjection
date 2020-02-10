<?php

namespace DI\Definitions;

class StaticDefinition implements Definition
{
    /**
     * @var object
     */
    private object $instance;

    /**
     * StaticDefinition constructor.
     * @param object $instance
     */
    public function __construct(object $instance)
    {
        $this->instance = $instance;
    }

    /**
     * @inheritDoc
     */
    public function resolve()
    {
        return $this->instance;
    }
}