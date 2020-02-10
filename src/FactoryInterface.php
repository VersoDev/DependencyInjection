<?php

namespace DI;

interface FactoryInterface
{
    /**
     * @return object
     */
    public function build(): object;
}