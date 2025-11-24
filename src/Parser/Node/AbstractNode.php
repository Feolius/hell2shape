<?php

namespace App\Parser\Node;

abstract class AbstractNode
{
    abstract public function __toString(): string;
}
