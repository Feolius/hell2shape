<?php

namespace App\Parser\Node;

final readonly class HashmapNode extends AbstractNode
{
    /**
     * @param array<HashmapItemNode> $items
     */
    public function __construct(public array $items)
    {
    }

    public function __toString(): string
    {
        $items = [];
        foreach ($this->items as $item) {
            $items[] = (string) $item;
        }

        return '{' . implode(', ', $items) . '}';
    }
}
