<?php

namespace Feolius\Hell2Shape\Generator;

use Feolius\Hell2Shape\Parser\Node\AbstractNode;

final readonly class Generator
{
    public function __construct(
        private GeneratorConfig $config,
    ) {
    }

    public function generate(AbstractNode $node): string
    {
        $visitor = new TypeGeneratorVisitor();
        $typeIR = $node->accept($visitor);

        $formatter = new Type\TypeFormatterVisitor($this->config);
        return $typeIR->accept($formatter);
    }
}
