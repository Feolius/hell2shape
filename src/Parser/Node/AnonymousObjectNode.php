<?php

namespace Feolius\Hell2Shape\Parser\Node;

final readonly class AnonymousObjectNode extends AbstractNode
{
    public function __construct()
    {
    }

    public function __toString(): string
    {
        return 'object(anonymous)';
    }

    public function accept(NodeVisitorInterface $visitor): mixed
    {
        return $visitor->visitAnonymousObject($this);
    }
}
