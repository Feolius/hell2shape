<?php

namespace App\Parser\Node;

abstract readonly class AbstractNode
{
    abstract public function __toString(): string;
}
