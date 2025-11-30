<?php

namespace App\Tests\Lexer;

use App\Lexer\Lexer;
use App\Lexer\Token;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(\App\Lexer\Lexer::class)]
class LexerTest extends TestCase
{
    #[DataProvider('sequenceDataProvider')]
    public function testTokenizeSequenceIsCorrect(string $input, array $tokenTypes): void
    {
        $lexer = new Lexer();
        $tokens = $lexer->tokenize($input);
        $resultTokenTypes = array_map(fn(Token $t) => $t->type, $tokens);
        self::assertEquals($tokenTypes, $resultTokenTypes);

    }

    public static function sequenceDataProvider(): array
    {
        return [
            ['[5]=>int(10)', [Lexer::T_INT_KEY, Lexer::T_ARROW, Lexer::T_INT, Lexer::T_END]],
            [<<<'END'
array(14) {
  ["a"]=>
  object(Playground\A)#1 (3) {
    ["prop"]=>
    string(4) "prop"
END, [Lexer::T_ARRAY, Lexer::T_WS, Lexer::T_OPEN_BRACE, Lexer::T_NEWLINE, Lexer::T_WS, Lexer::T_STRING_KEY, Lexer::T_ARROW,
                Lexer::T_NEWLINE, Lexer::T_WS, Lexer::T_OBJECT, Lexer::T_WS, Lexer::T_OPEN_BRACE, Lexer::T_NEWLINE,
                Lexer::T_WS, Lexer::T_STRING_KEY, Lexer::T_ARROW, Lexer::T_NEWLINE, Lexer::T_WS, Lexer::T_STRING, Lexer::T_END]],
        ];

    }

    public function testTokenizeOffsetIsCorrect(): void
    {
        $input = <<<'END'
array(1) {
  ["prop"]=>
    string(4) "prop"
}
END;

        $lexer = new Lexer();
        $tokens = $lexer->tokenize($input);
        self::assertEquals(Lexer::T_ARRAY, $tokens[0]->type);
        self::assertEquals($tokens[0]->line, 1);
        self::assertEquals($tokens[0]->column, 1);

        self::assertEquals($tokens[2]->type, Lexer::T_OPEN_BRACE);
        self::assertEquals($tokens[2]->line, 1);
        self::assertEquals($tokens[2]->column, 10);

        self::assertEquals($tokens[5]->type, Lexer::T_STRING_KEY);
        self::assertEquals($tokens[5]->line, 2);
        self::assertEquals($tokens[5]->column, 3);

        self::assertEquals($tokens[9]->type, Lexer::T_STRING);
        self::assertEquals($tokens[9]->line, 3);
        self::assertEquals($tokens[9]->column, 5);
    }

    public function testComplexArray(): void
    {
        $input = <<<'END'
array(14) {
  ["a"]=>
  object(Playground\A)#1 (3) {
    ["prop"]=>
    string(4) "prop"
    ["интProp":"Playground\A":private]=>
    int(9223372036854775807)
    ["prop2":protected]=>
    int(5)
  }
  [5]=>
  int(5)
  ["resource"]=>
  resource(5) of type (stream)
  ["test_int"]=>
  int(5)
  ["test_float"]=>
  float(7.999999999999999)
  ["test_arr"]=>
  array(3) {
    [0]=>
    int(1)
    [2]=>
    int(3)
    [3]=>
    object(Playground\BBвап)#2 (2) {
      ["prop":"Playground\BBвап":private]=>
      string(2) "bb"
      ["pubProp"]=>
      NULL
      ["a":protected]=>
      uninitialized(Playground\A)
      ["strProp":"Playground\BBвап":private]=>
      uninitialized(string)
    }
  }
  ["empty_array"]=>
  array(0) {
  }
  ["test_std"]=>
  object(stdClass)#4 (2) {
    ["prop"]=>
    int(5)
    ["prop2"]=>
    object(Playground\Inherited)#3 (3) {
      ["prop":"Playground\BBвап":private]=>
      string(2) "bb"
      ["pubProp"]=>
      NULL
      ["a":protected]=>
      object(Playground\A)#1 (3) {
        ["prop"]=>
        string(4) "prop"
        ["интProp":"Playground\A":private]=>
        int(9223372036854775807)
        ["prop2":protected]=>
        int(5)
      }
      ["strProp":"Playground\BBвап":private]=>
      uninitialized(string)
    }
  }
  ["test_obj"]=>
  object(stdClass)#5 (1) {
    ["scalar"]=>
    string(4) "gdfg"
  }
  ["test_bool"]=>
  bool(true)
  ["test_null"]=>
  NULL
  ["test_false"]=>
  bool(false)
  ["test_true"]=>
  bool(true)
  ["test_string"]=>
  string(10) "bal
dfgdfg"
}
END;
        $lexer = new Lexer();
        $tokens = $lexer->tokenize($input);
        $last = array_last($tokens);
        self::assertEquals(Lexer::T_END, $last->type);
    }
}
