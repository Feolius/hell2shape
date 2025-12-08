<?php

namespace App\Generator;

use App\Parser\Node\AbstractNode;

final class Generator
{
    public function __construct(
        private readonly KeyQuotingStyle $keyQuotingStyle = KeyQuotingStyle::NoQuotes,
        private readonly int $maxListUnionTypes = 3
    ) {
    }

    public function generate(AbstractNode $node): string
    {
        $visitor = new TypeGeneratorVisitor($this->keyQuotingStyle, $this->maxListUnionTypes);
        return $node->accept($visitor);
    }
}
