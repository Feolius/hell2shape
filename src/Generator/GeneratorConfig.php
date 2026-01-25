<?php

namespace Feolius\Hell2Shape\Generator;

final readonly class GeneratorConfig
{
    /**
     * @param  non-negative-int  $indentSize
     */
    public function __construct(
        public KeyQuotingStyle $keyQuotingStyle = KeyQuotingStyle::NoQuotes,
        public int $indentSize = 4,
        public ClassNameStyle $classNameStyle = ClassNameStyle::Unqualified,
        public bool $asDocComment = false,
    ) {
    }

}
