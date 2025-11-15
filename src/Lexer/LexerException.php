<?php

namespace App\Lexer;

class LexerException extends \Exception
{
    private const int TEXT_TRIM_LENGTH = 50;

    private(set) string $text;

    private(set) int $inputLine;

    private(set) int $inputColumn;

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
