<?php

namespace Feolius\Hell2Shape\Lexer;

class LexerException extends \Exception
{
    private const int TEXT_TRIM_LENGTH = 50;

    public private(set) string $text;

    public private(set) int $inputLine;

    public private(set) int $inputColumn;

    public function __construct(string $text, int $inputLine, int $inputColumn)
    {
        $this->text = $text;
        $this->inputLine = $inputLine;
        $this->inputColumn = $inputColumn;
        parent::__construct(sprintf(
            "Unable to define token at line %d and column %d: \n%s...",
            $inputLine,
            $inputColumn,
            mb_substr($text, 0, self::TEXT_TRIM_LENGTH)
        ));
    }
}
