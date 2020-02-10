<?php

namespace DI\Types;

interface Definition
{
    /**
     * @return mixed
     */
    public function resolve();
}