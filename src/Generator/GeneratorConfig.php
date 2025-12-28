<?php

namespace App\Generator;

final readonly class GeneratorConfig
{
    public function __construct(
        public KeyQuotingStyle $keyQuotingStyle = KeyQuotingStyle::NoQuotes,
        public int $indentSize = 4
    ) {
    }

}
