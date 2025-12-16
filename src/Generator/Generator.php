<?php

namespace App\Generator;

use App\Parser\Node\AbstractNode;

final class Generator
{
    public function __construct(
        private readonly KeyQuotingStyle $keyQuotingStyle = KeyQuotingStyle::NoQuotes
    ) {
    }

    public function generate(AbstractNode $node): string
    {
        $visitor = new TypeGeneratorVisitor();
        $typeIR = $node->accept($visitor);
        return $typeIR->toString($this->keyQuotingStyle);
    }
}
