<?php

namespace App\Lexer;

final readonly class Token
{
    public function __construct(
        public string $type,
        public string $value,
        public int $line,
        public int $column,
    )
    {
    }

}