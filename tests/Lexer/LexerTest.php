<?php

namespace Lexer;

use App\Lexer\Lexer;
use App\Lexer\Token;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(\App\Lexer\Lexer::class)]
class LexerTest extends TestCase
{
    #[DataProvider('dataProvider')]
    public function testTokenizeOriginalVarDump(string $input, array $tokens): void
    {
        $lexer = new Lexer();
        $result = $lexer->tokenize($input);
        $resultTokens = array_map(fn(Token $t) => $t->type, $result);
        self::assertEquals($tokens, $resultTokens);

    }

    public static function dataProvider(): array
    {
        return [
            ['[5]=>int(10)', [Lexer::T_INT_KEY, Lexer::T_ARROW, Lexer::T_INT]],
            [<<<'END'
array(14) {
  ["a"]=>
  object(Playground\A)#1 (3) {
    ["prop"]=>
    string(4) "prop"
END, [Lexer::T_ARRAY, Lexer::T_WS, Lexer::T_OPEN_BRACE, Lexer::T_NEWLINE, Lexer::T_WS, Lexer::T_STRING_KEY, Lexer::T_ARROW,
                Lexer::T_NEWLINE, Lexer::T_WS, Lexer::T_OBJECT, Lexer::T_WS, Lexer::T_OPEN_BRACE, Lexer::T_NEWLINE,
                Lexer::T_WS, Lexer::T_STRING_KEY, Lexer::T_ARROW, Lexer::T_NEWLINE, Lexer::T_WS, Lexer::T_STRING]]
        ];

    }
}
