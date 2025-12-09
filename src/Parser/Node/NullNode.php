<?php

namespace App\Parser\Node;

final readonly class NullNode extends AbstractNode
{
    public function __construct()
    {
    }

    public function __toString(): string
    {
        return 'null';
    }

    public function accept(NodeVisitorInterface $visitor): mixed
    {
        return $visitor->visitNull($this);
    }
}
