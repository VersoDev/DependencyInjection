<?php

namespace DI\Definitions;

class ScalarDefinition implements Definition
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * ScalarDefinition constructor.
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @inheritDoc
     */
    public function resolve()
    {
        return $this->value;
    }
}