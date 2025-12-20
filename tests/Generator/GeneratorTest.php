<?php

namespace App\Tests\Generator;

use App\Generator\Generator;
use App\Generator\KeyQuotingStyle;
use App\Lexer\Lexer;
use App\Parser\Parser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class GeneratorTest extends TestCase
{
    #[DataProvider('typeGenerationProvider')]
    public function testTypeGeneration(
        string $varDumpOutput,
        string $expectedType,
        KeyQuotingStyle $keyQuotingStyle = KeyQuotingStyle::NoQuotes
    ): void {
        $lexer = new Lexer();
        $parser = new Parser();
        $generator = new Generator($keyQuotingStyle);

        $tokens = $lexer->tokenize($varDumpOutput);
        $ast = $parser->parse($tokens);
        $result = $generator->generate($ast);

        $this->assertSame($expectedType, $result);
    }

    public static function typeGenerationProvider(): array
    {
        return [
            // Scalar types
            'bool true' => [
                'bool(true)',
                'bool',
            ],
            'bool false' => [
                'bool(false)',
                'bool',
            ],
            'int' => [
                'int(42)',
                'int',
            ],
            'float' => [
                'float(3.14)',
                'float',
            ],
            'string' => [
                'string(5) "hello"',
                'string',
            ],
            'null' => [
                'NULL',
                'null',
            ],
            'resource' => [
                'resource(5) of type (stream)',
                'resource',
            ],

            // Objects
            'object' => [
                'object(DateTime)#1 (3) {}',
                'DateTime',
            ],
            'anonymous object' => [
                'object(class@anonymous)#1 (0) {}',
                'object',
            ],

            // Simple list
            'list of ints' => [
                'array(3) {
  [0]=>
  int(1)
  [1]=>
  int(2)
  [2]=>
  int(3)
}',
                'list<int>',
            ],

            // List with union types (within threshold)
            'list with 2 types' => [
                'array(3) {
  [0]=>
  int(1)
  [1]=>
  string(5) "hello"
  [2]=>
  int(2)
}',
                'list<int|string>',
            ],

            'list with 3 types' => [
                'array(3) {
  [0]=>
  int(1)
  [1]=>
  string(5) "hello"
  [2]=>
  float(3.14)
}',
                'list<int|string|float>',
            ],

            // List with 4 types (no threshold anymore, all types shown)
            'list with 4 types' => [
                'array(4) {
  [0]=>
  int(1)
  [1]=>
  string(5) "hello"
  [2]=>
  float(3.14)
  [3]=>
  bool(true)
}',
                'list<int|string|float|bool>',
            ],

            // Empty array
            'empty array' => [
                'array(0) {
}',
                'array',
            ],

            // Hashmap (array shape)
            'simple hashmap' => [
                'array(2) {
  ["id"]=>
  int(1)
  ["name"]=>
  string(4) "test"
}',
                'array{id: int, name: string}',
            ],

            'hashmap with int keys' => [
                'array(2) {
  [0]=>
  string(5) "hello"
  [10]=>
  int(42)
}',
                'array{0: string, 10: int}',
            ],

            // Hashmap with single quotes
            'hashmap with single quotes' => [
                'array(2) {
  ["id"]=>
  int(1)
  ["name"]=>
  string(4) "test"
}',
                "array{'id': int, 'name': string}",
                KeyQuotingStyle::SingleQuotes,
            ],

            // Hashmap with double quotes
            'hashmap with double quotes' => [
                'array(2) {
  ["id"]=>
  int(1)
  ["name"]=>
  string(4) "test"
}',
                'array{"id": int, "name": string}',
                KeyQuotingStyle::DoubleQuotes,
            ],

            // StdObject
            'stdClass object' => [
                'object(stdClass)#1 (2) {
  ["id"]=>
  int(1)
  ["name"]=>
  string(4) "test"
}',
                'object{id: int, name: string}',
            ],

            // Nested structures
            'nested hashmap' => [
                'array(2) {
  ["user"]=>
  array(2) {
    ["id"]=>
    int(1)
    ["name"]=>
    string(4) "John"
  }
  ["active"]=>
  bool(true)
}',
                'array{user: array{id: int, name: string}, active: bool}',
            ],

            'hashmap with list' => [
                'array(2) {
  ["id"]=>
  int(1)
  ["tags"]=>
  array(2) {
    [0]=>
    string(3) "php"
    [1]=>
    string(3) "dev"
  }
}',
                'array{id: int, tags: list<string>}',
            ],

            'list of hashmaps' => [
                'array(2) {
  [0]=>
  array(1) {
    ["id"]=>
    int(1)
  }
  [1]=>
  array(1) {
    ["id"]=>
    int(2)
  }
}',
                'list<array{id: int}>',
            ],

            // Complex nested structure
            'complex nested' => [
                'array(3) {
  ["users"]=>
  array(2) {
    [0]=>
    array(2) {
      ["id"]=>
      int(1)
      ["tags"]=>
      array(2) {
        [0]=>
        string(3) "php"
        [1]=>
        string(2) "js"
      }
    }
    [1]=>
    array(2) {
      ["id"]=>
      int(2)
      ["tags"]=>
      array(1) {
        [0]=>
        string(6) "python"
      }
    }
  }
  ["count"]=>
  int(2)
  ["active"]=>
  bool(true)
}',
                'array{users: list<array{id: int, tags: list<string>}>, count: int, active: bool}',
            ],

            // Hashmap merging tests
            'list with hashmaps - missing key becomes optional' => [
                'array(2) {
  [0]=>
  array(2) {
    ["k1"]=>
    string(5) "hello"
    ["k2"]=>
    int(42)
  }
  [1]=>
  array(1) {
    ["k1"]=>
    string(5) "world"
  }
}',
                'list<array{k1: string, k2?: int}>',
            ],

            'list with hashmaps - different types create union' => [
                'array(2) {
  [0]=>
  array(1) {
    ["k1"]=>
    string(5) "hello"
  }
  [1]=>
  array(1) {
    ["k1"]=>
    int(42)
  }
}',
                'list<array{k1: string|int}>',
            ],

            'list with hashmaps - nested hashmap merging' => [
                'array(2) {
  [0]=>
  array(2) {
    ["a"]=>
    int(1)
    ["b"]=>
    array(1) {
      ["x"]=>
      string(2) "hi"
    }
  }
  [1]=>
  array(2) {
    ["a"]=>
    int(2)
    ["b"]=>
    array(1) {
      ["y"]=>
      int(5)
    }
  }
}',
                'list<array{a: int, b: array{x?: string, y?: int}}>',
            ],

            'list with mixed types - hashmaps merge, scalars create union' => [
                'array(4) {
  [0]=>
  array(2) {
    ["a"]=>
    int(1)
    ["b"]=>
    int(2)
  }
  [1]=>
  string(5) "hello"
  [2]=>
  array(1) {
    ["a"]=>
    int(3)
  }
  [3]=>
  int(42)
}',
                'list<string|int|array{a: int, b?: int}>',
            ],

            'list with stdObjects - merging works' => [
                'array(2) {
  [0]=>
  object(stdClass)#1 (2) {
    ["k1"]=>
    string(5) "hello"
    ["k2"]=>
    int(42)
  }
  [1]=>
  object(stdClass)#2 (1) {
    ["k1"]=>
    string(5) "world"
  }
}',
                'list<object{k1: string, k2?: int}>',
            ],
        ];
    }
}
