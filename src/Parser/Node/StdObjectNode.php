<?php

namespace App\Parser\Node;

use App\Generator\TypeGeneratorVisitor;

final readonly class StdObjectNode extends AbstractNode
{
    /**
     * @param array<StdObjectItemNode> $items
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

        return '(stdClass) {'.implode(', ', $items).'}';
    }

    public function accept(TypeGeneratorVisitor $visitor): string
    {
        return $visitor->visitStdObject($this);
    }
}
