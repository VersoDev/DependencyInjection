<?php

namespace DI\Definitions;

class ArrayDefinition implements Definition
{
    /**
     * @var array
     */
    private array $values;

    /**
     * ArrayDefinition constructor.
     * @param array $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * @inheritDoc
     */
    public function resolve(): array
    {
        return $this->values;
    }
}