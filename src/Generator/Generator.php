<?php

namespace App\Generator;

use App\Parser\Node\AbstractNode;

final class Generator
{
    public function __construct(
        private readonly KeyQuotingStyle $keyQuotingStyle = KeyQuotingStyle::NoQuotes,
        private readonly int $indentSize = 4
    ) {
    }

    public function generate(AbstractNode $node): string
    {
        $visitor = new TypeGeneratorVisitor();
        $typeIR = $node->accept($visitor);

        $formatter = new Type\TypeFormatterVisitor($this->keyQuotingStyle, $this->indentSize);
        return $typeIR->accept($formatter);
    }
}
