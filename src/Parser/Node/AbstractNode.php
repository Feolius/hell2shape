<?php

namespace Feolius\Hell2Shape\Parser\Node;

abstract readonly class AbstractNode
{
    abstract public function __toString(): string;

    /**
     * Accept a visitor to traverse this node.
     *
     * @template R
     * @param NodeVisitorInterface<R> $visitor
     * @return R
     */
    abstract public function accept(NodeVisitorInterface $visitor): mixed;
}
