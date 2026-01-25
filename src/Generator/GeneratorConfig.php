<?php

namespace Feolius\Hell2Shape\Generator;

final readonly class GeneratorConfig
{
    /**
     * @param  non-negative-int  $indentSize
     */
    public function __construct(
        public bool $asDocComment = true,
        public KeyQuotingStyle $keyQuotingStyle = KeyQuotingStyle::NoQuotes,
        public int $indentSize = 4,
        public ClassNameStyle $classNameStyle = ClassNameStyle::Unqualified,
    ) {
    }

    /**
     * Create a config without doc comment formatting
     *
     * @param  non-negative-int  $indentSize
     */
    public static function withoutDocComment(
        KeyQuotingStyle $keyQuotingStyle = KeyQuotingStyle::NoQuotes,
        int $indentSize = 4,
        ClassNameStyle $classNameStyle = ClassNameStyle::Unqualified,
    ): self {
        return new self(false, $keyQuotingStyle, $indentSize, $classNameStyle);
    }
}
