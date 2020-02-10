<?php

namespace DI\Definitions;

interface Definition
{
    /**
     * @return mixed
     */
    public function resolve();
}