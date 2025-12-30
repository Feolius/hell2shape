<?php

namespace App\Tests\Lexer;

use Feolius\Hell2Shape\Lexer\Lexer;
use Feolius\Hell2Shape\Lexer\Token;
use Feolius\Hell2Shape\Tests\VarDumper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class A
{
    public string $prop;

    private readonly int $интProp;

    private $obj;

    public function __construct(
        protected int $prop2
    ) {
        $this->интProp = PHP_INT_MAX;
        $this->obj = new class() {
            private string $test = 'sfsd';
        };
    }
}

class BBвап
{
    private string $prop;

    public $pubProp;

    protected A $a;

    private readonly string $strProp;

    public function __construct()
    {
        $this->prop = 'bb';
    }

    public function getProp(): int
    {
        return $this->prop;

    }
}

class Inherited extends BBвап
{
    public function __construct(
        A $a
    ) {
        parent::__construct();
        $this->a = $a;
    }
}

#[CoversClass(\Feolius\Hell2Shape\Lexer\Lexer::class)]
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
        $a = new A(5);
        $b = new BBвап();
        $testArr = [1, 2, 3, $b];
        unset($testArr[1]);
        $inh = new Inherited($a);

        $stdObj = new \stdClass();
        $stdObj->prop = 5;
        $stdObj->prop2 = $inh;
        $f = fopen("php://stdin", "r");
        $input = VarDumper::dump([
            'a' => $a,
            5 => 5,
            'resource' => $f,
            'test_int' => 5,
            'test_float' => 7.9999999999999991118,
            'test_arr' => $testArr,
            'empty_array' => [],
            'test_std' => $stdObj,
            'test_obj' => (object)"gdfg",
            'test_bool' => true,
            'test_null' => null,
            'test_false' => false,
            'test_true' => true,
            "test_string" => "bal\ndfgdfg",
        ]);
        $lexer = new Lexer();
        $tokens = $lexer->tokenize($input);
        $last = array_last($tokens);
        self::assertEquals(Lexer::T_END, $last->type);
    }
}
