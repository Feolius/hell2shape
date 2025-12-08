<?php

namespace App\Parser\Node;

use App\Generator\TypeGeneratorVisitor;

final readonly class ListNode extends AbstractNode
{
    /**
     * @param list<ListItemNode> $items
     */
    public function __construct(
        public array $items
    ) {
    }

    public function __toString(): string
    {
        $items = [];
        foreach ($this->items as $item) {
            $items[] = (string)$item;
        }

        return '['.implode(', ', $items).']';
    }

    public function accept(TypeGeneratorVisitor $visitor): string
    {
        return $visitor->visitList($this);
    }
}
