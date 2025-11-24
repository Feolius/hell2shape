<?php

namespace App\Parser\Node;

class NullNode extends AbstractNode
{
    public function __construct()
    {
    }

    public function __toString(): string
    {
        return 'null';
    }
}
