<?php

namespace Feolius\Hell2Shape\Generator;

final readonly class GeneratorConfig
{
    public function __construct(
        public KeyQuotingStyle $keyQuotingStyle = KeyQuotingStyle::NoQuotes,
        public int $indentSize = 4,
        public ClassNameStyle $classNameStyle = ClassNameStyle::Unqualified,
    ) {
    }

}
