<?php

namespace App\Tests\Generator;

use App\Generator\Generator;
use App\Generator\GeneratorConfig;
use App\Generator\KeyQuotingStyle;
use App\Lexer\Lexer;
use App\Parser\Parser;
use PHPUnit\Framework\TestCase;

final class FormattingTest extends TestCase
{
    public function testFormattedOutput(): void
    {
        $varDump = <<<'VARDUMP'
array(3) {
  ["user"]=>
  array(3) {
    ["id"]=>
    int(1)
    ["name"]=>
    string(4) "John"
    ["email"]=>
    string(14) "john@example.com"
  }
  ["tags"]=>
  array(2) {
    [0]=>
    string(3) "php"
    [1]=>
    string(3) "dev"
  }
  ["active"]=>
  bool(true)
}
VARDUMP;

        $expected = <<<'EXPECTED'
array{
    user: array{
        id: int,
        name: string,
        email: string
    },
    tags: list<string>,
    active: bool
}
EXPECTED;

        $lexer = new Lexer();
        $parser = new Parser();
        $config = new GeneratorConfig(KeyQuotingStyle::NoQuotes, indentSize: 4);
        $generator = new Generator($config);

        $tokens = $lexer->tokenize($varDump);
        $ast = $parser->parse($tokens);
        $result = $generator->generate($ast);

        $this->assertSame($expected, $result);
    }

    public function testFormattedOutputWithSingleQuotes(): void
    {
        $varDump = <<<'VARDUMP'
array(2) {
  ["id"]=>
  int(1)
  ["name"]=>
  string(4) "test"
}
VARDUMP;

        $expected = <<<'EXPECTED'
array{
    'id': int,
    'name': string
}
EXPECTED;

        $lexer = new Lexer();
        $parser = new Parser();
        $config = new GeneratorConfig(KeyQuotingStyle::SingleQuotes, indentSize: 4);
        $generator = new Generator($config);

        $tokens = $lexer->tokenize($varDump);
        $ast = $parser->parse($tokens);
        $result = $generator->generate($ast);

        $this->assertSame($expected, $result);
    }

    public function testFormattedOutputWithCustomIndent(): void
    {
        $varDump = <<<'VARDUMP'
array(2) {
  ["user"]=>
  array(2) {
    ["id"]=>
    int(1)
    ["name"]=>
    string(4) "John"
  }
  ["active"]=>
  bool(true)
}
VARDUMP;

        $expected = <<<'EXPECTED'
array{
  user: array{
    id: int,
    name: string
  },
  active: bool
}
EXPECTED;

        $lexer = new Lexer();
        $parser = new Parser();
        $config = new GeneratorConfig(KeyQuotingStyle::NoQuotes, indentSize: 2);
        $generator = new Generator($config);

        $tokens = $lexer->tokenize($varDump);
        $ast = $parser->parse($tokens);
        $result = $generator->generate($ast);

        $this->assertSame($expected, $result);
    }

    public function testIntegerKeysWithSingleQuotes(): void
    {
        $varDump = <<<'VARDUMP'
array(3) {
  [0]=>
  string(3) "foo"
  [5]=>
  string(3) "bar"
  [10]=>
  int(42)
}
VARDUMP;

        $expected = <<<'EXPECTED'
array{
    0: string,
    5: string,
    10: int
}
EXPECTED;

        $lexer = new Lexer();
        $parser = new Parser();
        $config = new GeneratorConfig(KeyQuotingStyle::SingleQuotes, indentSize: 4);
        $generator = new Generator($config);

        $tokens = $lexer->tokenize($varDump);
        $ast = $parser->parse($tokens);
        $result = $generator->generate($ast);

        $this->assertSame($expected, $result);
    }

    public function testMixedIntegerAndStringKeysWithDoubleQuotes(): void
    {
        $varDump = <<<'VARDUMP'
array(4) {
  [0]=>
  string(3) "foo"
  ["name"]=>
  string(4) "John"
  [10]=>
  int(42)
  ["email"]=>
  string(14) "john@example.com"
}
VARDUMP;

        $expected = <<<'EXPECTED'
array{
    0: string,
    "name": string,
    10: int,
    "email": string
}
EXPECTED;

        $lexer = new Lexer();
        $parser = new Parser();
        $config = new GeneratorConfig(KeyQuotingStyle::DoubleQuotes, indentSize: 4);
        $generator = new Generator($config);

        $tokens = $lexer->tokenize($varDump);
        $ast = $parser->parse($tokens);
        $result = $generator->generate($ast);

        $this->assertSame($expected, $result);
    }
}
