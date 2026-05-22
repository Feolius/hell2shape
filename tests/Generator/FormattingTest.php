<?php

namespace Feolius\Hell2Shape\Tests\Generator;

use Feolius\Hell2Shape\Generator\ClassNameStyle;
use Feolius\Hell2Shape\Generator\Generator;
use Feolius\Hell2Shape\Generator\GeneratorConfig;
use Feolius\Hell2Shape\Generator\KeyQuotingStyle;
use Feolius\Hell2Shape\Lexer\Lexer;
use Feolius\Hell2Shape\Parser\Parser;
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
        email: string,
    },
    tags: list<string>,
    active: bool,
}
EXPECTED;

        $lexer = new Lexer();
        $config = GeneratorConfig::withoutDocComment(KeyQuotingStyle::NoQuotes, indentSize: 4);
        $generator = new Generator($config);

        $tokens = $lexer->tokenize($varDump);
        $ast = Parser::parse($tokens);
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
    'name': string,
}
EXPECTED;

        $lexer = new Lexer();
        $config = GeneratorConfig::withoutDocComment(KeyQuotingStyle::SingleQuotes, indentSize: 4);
        $generator = new Generator($config);

        $tokens = $lexer->tokenize($varDump);
        $ast = Parser::parse($tokens);
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
    name: string,
  },
  active: bool,
}
EXPECTED;

        $lexer = new Lexer();
        $config = GeneratorConfig::withoutDocComment(KeyQuotingStyle::NoQuotes, indentSize: 2);
        $generator = new Generator($config);

        $tokens = $lexer->tokenize($varDump);
        $ast = Parser::parse($tokens);
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
    10: int,
}
EXPECTED;

        $lexer = new Lexer();
        $config = GeneratorConfig::withoutDocComment(KeyQuotingStyle::SingleQuotes, indentSize: 4);
        $generator = new Generator($config);

        $tokens = $lexer->tokenize($varDump);
        $ast = Parser::parse($tokens);
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
    "email": string,
}
EXPECTED;

        $lexer = new Lexer();
        $config = GeneratorConfig::withoutDocComment(KeyQuotingStyle::DoubleQuotes, indentSize: 4);
        $generator = new Generator($config);

        $tokens = $lexer->tokenize($varDump);
        $ast = Parser::parse($tokens);
        $result = $generator->generate($ast);

        $this->assertSame($expected, $result);
    }

    public function testClassNameStyleUnqualified(): void
    {
        $varDump = <<<'VARDUMP'
object(App\Models\User)#1 (2) {
  ["id"]=>
  int(1)
  ["name"]=>
  string(4) "John"
}
VARDUMP;

        $expected = 'User';

        $lexer = new Lexer();
        $config = GeneratorConfig::withoutDocComment(
            KeyQuotingStyle::NoQuotes,
            indentSize: 4,
            classNameStyle: ClassNameStyle::Unqualified
        );
        $generator = new Generator($config);

        $tokens = $lexer->tokenize($varDump);
        $ast = Parser::parse($tokens);
        $result = $generator->generate($ast);

        $this->assertSame($expected, $result);
    }

    public function testClassNameStyleQualified(): void
    {
        $varDump = <<<'VARDUMP'
object(App\Models\User)#1 (2) {
  ["id"]=>
  int(1)
  ["name"]=>
  string(4) "John"
}
VARDUMP;

        $expected = 'App\Models\User';

        $lexer = new Lexer();
        $config = GeneratorConfig::withoutDocComment(
            KeyQuotingStyle::NoQuotes,
            indentSize: 4,
            classNameStyle: ClassNameStyle::Qualified
        );
        $generator = new Generator($config);

        $tokens = $lexer->tokenize($varDump);
        $ast = Parser::parse($tokens);
        $result = $generator->generate($ast);

        $this->assertSame($expected, $result);
    }

    public function testClassNameStyleFullyQualified(): void
    {
        $varDump = <<<'VARDUMP'
object(App\Models\User)#1 (2) {
  ["id"]=>
  int(1)
  ["name"]=>
  string(4) "John"
}
VARDUMP;

        $expected = '\App\Models\User';

        $lexer = new Lexer();
        $config = GeneratorConfig::withoutDocComment(
            KeyQuotingStyle::NoQuotes,
            indentSize: 4,
            classNameStyle: ClassNameStyle::FullyQualified
        );
        $generator = new Generator($config);

        $tokens = $lexer->tokenize($varDump);
        $ast = Parser::parse($tokens);
        $result = $generator->generate($ast);

        $this->assertSame($expected, $result);
    }

    public function testClassNameStyleWithNestedObjects(): void
    {
        $varDump = <<<'VARDUMP'
array(2) {
  ["user"]=>
  object(App\Models\User)#1 (1) {
    ["id"]=>
    int(1)
  }
  ["post"]=>
  object(App\Models\Post)#2 (1) {
    ["title"]=>
    string(5) "Hello"
  }
}
VARDUMP;

        $expected = <<<'EXPECTED'
array{
    user: User,
    post: Post,
}
EXPECTED;

        $lexer = new Lexer();
        $config = GeneratorConfig::withoutDocComment(
            KeyQuotingStyle::NoQuotes,
            indentSize: 4,
            classNameStyle: ClassNameStyle::Unqualified
        );
        $generator = new Generator($config);

        $tokens = $lexer->tokenize($varDump);
        $ast = Parser::parse($tokens);
        $result = $generator->generate($ast);

        $this->assertSame($expected, $result);
    }

    public function testAsDocCommentWithSimpleScalar(): void
    {
        $varDump = <<<'VARDUMP'
int(42)
VARDUMP;

        $expected = '/** int */';

        $lexer = new Lexer();
        $config = new GeneratorConfig(
            asDocComment: true,
            keyQuotingStyle: KeyQuotingStyle::NoQuotes,
            indentSize: 4,
            classNameStyle: ClassNameStyle::Unqualified,
        );
        $generator = new Generator($config);

        $tokens = $lexer->tokenize($varDump);
        $ast = Parser::parse($tokens);
        $result = $generator->generate($ast);

        $this->assertSame($expected, $result);
    }

    public function testAsDocCommentWithString(): void
    {
        $varDump = <<<'VARDUMP'
string(5) "hello"
VARDUMP;

        $expected = '/** string */';

        $lexer = new Lexer();
        $config = new GeneratorConfig(
            asDocComment: true,
            keyQuotingStyle: KeyQuotingStyle::NoQuotes,
            indentSize: 4,
            classNameStyle: ClassNameStyle::Unqualified,
        );
        $generator = new Generator($config);

        $tokens = $lexer->tokenize($varDump);
        $ast = Parser::parse($tokens);
        $result = $generator->generate($ast);

        $this->assertSame($expected, $result);
    }

    public function testAsDocCommentWithUnionType(): void
    {
        $varDump = <<<'VARDUMP'
array(2) {
  [0]=>
  int(1)
  [1]=>
  string(4) "test"
}
VARDUMP;

        $expected = '/** list<int|string> */';

        $lexer = new Lexer();
        $config = new GeneratorConfig(
            asDocComment: true,
            keyQuotingStyle: KeyQuotingStyle::NoQuotes,
            indentSize: 4,
            classNameStyle: ClassNameStyle::Unqualified
        );
        $generator = new Generator($config);

        $tokens = $lexer->tokenize($varDump);
        $ast = Parser::parse($tokens);
        $result = $generator->generate($ast);

        $this->assertSame($expected, $result);
    }

    public function testAsDocCommentWithSimpleList(): void
    {
        $varDump = <<<'VARDUMP'
array(3) {
  [0]=>
  string(3) "foo"
  [1]=>
  string(3) "bar"
  [2]=>
  string(3) "baz"
}
VARDUMP;

        $expected = '/** list<string> */';

        $lexer = new Lexer();
        $config = new GeneratorConfig(
            asDocComment: true,
            keyQuotingStyle: KeyQuotingStyle::NoQuotes,
            indentSize: 4,
            classNameStyle: ClassNameStyle::Unqualified
        );
        $generator = new Generator($config);

        $tokens = $lexer->tokenize($varDump);
        $ast = Parser::parse($tokens);
        $result = $generator->generate($ast);

        $this->assertSame($expected, $result);
    }

    public function testAsDocCommentWithArrayStructure(): void
    {
        $varDump = <<<'VARDUMP'
array(3) {
  ["id"]=>
  int(1)
  ["name"]=>
  string(4) "John"
  ["active"]=>
  bool(true)
}
VARDUMP;

        $expected = <<<'EXPECTED'
/**
 * array{
 *     id: int,
 *     name: string,
 *     active: bool,
 * }
 */
EXPECTED;

        $lexer = new Lexer();
        $config = new GeneratorConfig(
            asDocComment: true,
            keyQuotingStyle: KeyQuotingStyle::NoQuotes,
            indentSize: 4,
            classNameStyle: ClassNameStyle::Unqualified
        );
        $generator = new Generator($config);

        $tokens = $lexer->tokenize($varDump);
        $ast = Parser::parse($tokens);
        $result = $generator->generate($ast);

        $this->assertSame($expected, $result);
    }

    public function testAsDocCommentWithNestedArrayStructure(): void
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
/**
 * array{
 *     user: array{
 *         id: int,
 *         name: string,
 *     },
 *     active: bool,
 * }
 */
EXPECTED;

        $lexer = new Lexer();
        $config = new GeneratorConfig(
            asDocComment: true,
            keyQuotingStyle: KeyQuotingStyle::NoQuotes,
            indentSize: 4,
            classNameStyle: ClassNameStyle::Unqualified
        );
        $generator = new Generator($config);

        $tokens = $lexer->tokenize($varDump);
        $ast = Parser::parse($tokens);
        $result = $generator->generate($ast);

        $this->assertSame($expected, $result);
    }

    public function testAsDocCommentWithSingleQuotes(): void
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
/**
 * array{
 *     'id': int,
 *     'name': string,
 * }
 */
EXPECTED;

        $lexer = new Lexer();
        $config = new GeneratorConfig(
            asDocComment: true,
            keyQuotingStyle: KeyQuotingStyle::SingleQuotes,
            indentSize: 4,
            classNameStyle: ClassNameStyle::Unqualified
        );
        $generator = new Generator($config);

        $tokens = $lexer->tokenize($varDump);
        $ast = Parser::parse($tokens);
        $result = $generator->generate($ast);

        $this->assertSame($expected, $result);
    }

    public function testAsDocCommentWithCustomIndent(): void
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
/**
 * array{
 *   id: int,
 *   name: string,
 * }
 */
EXPECTED;

        $lexer = new Lexer();
        $config = new GeneratorConfig(
            asDocComment: true,
            keyQuotingStyle: KeyQuotingStyle::NoQuotes,
            indentSize: 2,
            classNameStyle: ClassNameStyle::Unqualified
        );
        $generator = new Generator($config);

        $tokens = $lexer->tokenize($varDump);
        $ast = Parser::parse($tokens);
        $result = $generator->generate($ast);

        $this->assertSame($expected, $result);
    }

    public function testAsDocCommentWithClassName(): void
    {
        $varDump = <<<'VARDUMP'
object(App\Models\User)#1 (2) {
  ["id"]=>
  int(1)
  ["name"]=>
  string(4) "John"
}
VARDUMP;

        $expected = '/** User */';

        $lexer = new Lexer();
        $config = new GeneratorConfig(
            asDocComment: true,
            keyQuotingStyle: KeyQuotingStyle::NoQuotes,
            indentSize: 4,
            classNameStyle: ClassNameStyle::Unqualified
        );
        $generator = new Generator($config);

        $tokens = $lexer->tokenize($varDump);
        $ast = Parser::parse($tokens);
        $result = $generator->generate($ast);

        $this->assertSame($expected, $result);
    }

    public function testAsDocCommentWithFullyQualifiedClassName(): void
    {
        $varDump = <<<'VARDUMP'
object(App\Models\User)#1 (2) {
  ["id"]=>
  int(1)
  ["name"]=>
  string(4) "John"
}
VARDUMP;

        $expected = '/** \App\Models\User */';

        $lexer = new Lexer();
        $config = new GeneratorConfig(
            asDocComment: true,
            keyQuotingStyle: KeyQuotingStyle::NoQuotes,
            indentSize: 4,
            classNameStyle: ClassNameStyle::FullyQualified
        );
        $generator = new Generator($config);

        $tokens = $lexer->tokenize($varDump);
        $ast = Parser::parse($tokens);
        $result = $generator->generate($ast);

        $this->assertSame($expected, $result);
    }

    public function testAsDocCommentWithZeroIndent(): void
    {
        $varDump = <<<'VARDUMP'
array(2) {
  ["id"]=>
  int(1)
  ["name"]=>
  string(4) "test"
}
VARDUMP;

        $expected = '/** array{id: int, name: string} */';

        $lexer = new Lexer();
        $config = new GeneratorConfig(
            asDocComment: true,
            keyQuotingStyle: KeyQuotingStyle::NoQuotes,
            indentSize: 0,
            classNameStyle: ClassNameStyle::Unqualified
        );
        $generator = new Generator($config);

        $tokens = $lexer->tokenize($varDump);
        $ast = Parser::parse($tokens);
        $result = $generator->generate($ast);

        $this->assertSame($expected, $result);
    }

    public function testAsDocCommentWithComplexNestedStructure(): void
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
/**
 * array{
 *     user: array{
 *         id: int,
 *         name: string,
 *         email: string,
 *     },
 *     tags: list<string>,
 *     active: bool,
 * }
 */
EXPECTED;

        $lexer = new Lexer();
        $config = new GeneratorConfig(
            asDocComment: true,
            keyQuotingStyle: KeyQuotingStyle::NoQuotes,
            indentSize: 4,
            classNameStyle: ClassNameStyle::Unqualified
        );
        $generator = new Generator($config);

        $tokens = $lexer->tokenize($varDump);
        $ast = Parser::parse($tokens);
        $result = $generator->generate($ast);

        $this->assertSame($expected, $result);
    }
}
